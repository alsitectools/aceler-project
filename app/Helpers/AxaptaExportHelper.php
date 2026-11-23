<?php

namespace App\Helpers;

use App\Models\ExportBatch;
use App\Models\ExportLedgerLine;
use App\Models\MasterObra;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\PuntuacionTarea;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Str;

/**
 * Export de horas a Axapta.
 *
 * Formato (igual que los ficheros que Axapta acepta): ancho fijo, 234 caracteres
 * por línea, ASCII, CRLF y sin cabecera. Una línea por tarea y día trabajado del
 * empleado asignado a un encargo finalizado (status 4) de un proyecto de Obra.
 *
 * Funciona por diferencias: el ledger (export_ledger_lines) guarda lo ya enviado
 * por tarea y día, y cada export solo manda lo que ha cambiado desde entonces
 * (líneas negativas para lo que se ha quitado). Los lotes marcados como
 * rechazados (export_batches.rejected_at) no cuentan como enviados.
 *
 * Fórmula (en segundos, como el fichero original: HrDecimal = h + m/60 + s/3600):
 *   Imputado(k, d) = Σ (h·3600 + m·60 + s) de las imputaciones del empleado en la tarea k el día d
 *   Enviado(k, d)  = Σ round(horas_decimales · 3600) de lo ya exportado para esa tarea y día
 *   Δ(k, d)        = Imputado − Enviado  →  Horas = Δ en HH:MM:SS · HrDecimal = Δ / 3600
 * Puntos: la puntuación de la tarea (puntuacion_tarea.cantidad_puntaje, 2 decimales), como en el
 * fichero original (un valor fijo por referencia). Si no tiene, los puntos automáticos del encargo
 * (horas imputadas + puntos por tipo de tarea, ver MilestonePointsHelper). Vacío si no hay horas.
 */
class AxaptaExportHelper
{
    /** Campos de la línea: [nombre, ancho, alineación]. Suma 234. */
    public const LAYOUT = [
        ['regId', 10, STR_PAD_LEFT],
        ['fecha', 10, STR_PAD_RIGHT],
        ['empresa', 20, STR_PAD_RIGHT],
        ['delegacion', 20, STR_PAD_RIGHT],
        ['empleado', 20, STR_PAD_RIGHT],
        ['masterobrasid', 20, STR_PAD_RIGHT],
        ['obra', 20, STR_PAD_RIGHT],
        ['descripcion', 50, STR_PAD_RIGHT],
        ['op', 10, STR_PAD_LEFT],
        ['horas', 10, STR_PAD_RIGHT],
        ['ref', 14, STR_PAD_RIGHT],
        ['linea', 10, STR_PAD_LEFT],
        ['hrDecimal', 10, STR_PAD_RIGHT],
        ['puntos', 10, STR_PAD_RIGHT],
    ];

    public const LINE_LENGTH = 234;

    private static ?array $rejectedBatchIds = null;
    private static array $taskCache = [];
    private static array $pointsCache = [];
    // Precargados por buildExport() para no consultar encargo a encargo
    private static ?array $puntuacionByTask = null;   // id_tarea => cantidad_puntaje (null si no tiene)
    private static array $milestoneCache = [];        // id => Milestone
    private static array $milestonePointsCache = [];  // id => puntos automáticos del encargo (o null)
    private static ?array $businessUnitByProject = null; // project_id => business_unit de su master obra
    private static ?array $businessUnitByRefMo = null;   // ref_mo => business_unit

    /**
     * "HH:MM[:SS]" a horas decimales: h + m/60 + s/3600 (misma fórmula que el fichero original de Axapta)
     */
    public static function timeToDecimal($timeValue): float
    {
        return self::timeToSeconds($timeValue) / 3600;
    }

    /**
     * "HH:MM[:SS]" a segundos: h·3600 + m·60 + s
     */
    public static function timeToSeconds($timeValue): int
    {
        if (!$timeValue || strpos((string) $timeValue, ':') === false) {
            return 0;
        }

        $parts = explode(':', (string) $timeValue);

        return ((int) ($parts[0] ?? 0)) * 3600 + ((int) ($parts[1] ?? 0)) * 60 + (int) ($parts[2] ?? 0);
    }

    /**
     * Segundos a "HH:MM:SS" (con signo si es negativo)
     */
    public static function secondsToTimeFormat(int $seconds): string
    {
        $abs = abs($seconds);
        $time = sprintf('%02d:%02d:%02d', intdiv($abs, 3600), intdiv($abs % 3600, 60), $abs % 60);

        return $seconds < 0 ? '-' . $time : $time;
    }

    /**
     * Convierte "HH:MM[:SS]" a minutos enteros (los segundos se ignoran)
     */
    public static function timeToMinutes($timeValue): int
    {
        if (!$timeValue || strpos((string) $timeValue, ':') === false) {
            return 0;
        }

        $parts = explode(':', (string) $timeValue);

        return ((int) ($parts[0] ?? 0)) * 60 + (int) ($parts[1] ?? 0);
    }

    /**
     * Minutos a "HH:MM:SS" (con signo si es negativo)
     */
    public static function minutesToTimeFormat(int $minutes): string
    {
        $abs = abs($minutes);
        $time = sprintf('%02d:%02d:00', intdiv($abs, 60), $abs % 60);

        return $minutes < 0 ? '-' . $time : $time;
    }

    /**
     * Convierte horas decimales a formato HH:MM:SS (soporta negativos)
     */
    public static function decimalToTimeFormat(float $decimalHours): string
    {
        return self::minutesToTimeFormat((int) round($decimalHours * 60));
    }

    /**
     * Texto apto para Axapta: ASCII imprimible, en una sola línea
     */
    public static function asciiText($value): string
    {
        $text = Str::ascii((string) $value);
        $text = preg_replace('/[^\x20-\x7E]/', '', $text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Referencia tal como se manda a Axapta: la de la app sin la letra O/X
     * (con M.O / sin M.O), que solo es una ayuda para el usuario.
     * 26ENO004D1.5 => 26EN004D1.5. El año de 2 cifras se mantiene.
     */
    public static function axaptaReference($referencia, string $delegacion): string
    {
        $ref = self::asciiText($referencia);
        if ($ref === '' || $delegacion === '') {
            return $ref;
        }

        return preg_replace('/^(\d{2}' . preg_quote(strtoupper($delegacion), '/') . ')[OX]/i', '$1', $ref, 1);
    }

    /**
     * Compone una línea de ancho fijo. Cada campo se recorta o rellena a su ancho exacto.
     */
    public static function formatLine(array $fields): string
    {
        $line = '';
        foreach (self::LAYOUT as [$name, $width, $align]) {
            $value = self::asciiText($fields[$name] ?? '');
            $value = substr($value, 0, $width);
            $line .= str_pad($value, $width, ' ', $align);
        }

        if (strlen($line) !== self::LINE_LENGTH) {
            throw new \LogicException('Línea de export Axapta con longitud ' . strlen($line));
        }

        return $line;
    }

    /**
     * Delegación efectiva de un proyecto: ref_delegation si tiene valor;
     * si no, el business_unit de su master obra.
     */
    public static function resolveProjectDelegationId($project): ?string
    {
        if (!$project) {
            return null;
        }

        $delegationId = trim((string) ($project->ref_delegation ?? ''));

        if ($delegationId !== '') {
            return $delegationId;
        }

        // Precargado por buildExport(); si no, se consulta aquí
        if (self::$businessUnitByProject !== null) {
            $businessUnit = self::$businessUnitByProject[$project->id] ?? null;
            if (empty($businessUnit) && !empty($project->ref_mo)) {
                $businessUnit = self::$businessUnitByRefMo[$project->ref_mo] ?? null;
            }
        } else {
            $businessUnit = MasterObra::where('project_id', $project->id)
                ->whereNotNull('business_unit')
                ->where('business_unit', '!=', '')
                ->value('business_unit');

            if (empty($businessUnit) && !empty($project->ref_mo)) {
                $businessUnit = MasterObra::where('ref_mo', $project->ref_mo)
                    ->whereNotNull('business_unit')
                    ->where('business_unit', '!=', '')
                    ->value('business_unit');
            }
        }

        $businessUnit = trim((string) ($businessUnit ?? ''));

        return $businessUnit !== '' ? $businessUnit : null;
    }

    /**
     * Obtiene datos del proyecto y workspace para la exportación
     */
    public static function getProjectExportData($project, $workspace = false)
    {
        // $workspace precargado por buildExport(); false = buscarlo aquí
        if ($workspace === false) {
            $workspace = Workspace::find($project->workspace);
        }
        $delegationId = strtoupper((string) (self::resolveProjectDelegationId($project)
            ?? $workspace?->delegation_id
            ?? ''));

        return (object)[
            'delegacion' => $delegationId,
            'empresa' => config("axapta.empresa_por_delegacion.$delegationId", ''),
            'masterobrasid' => trim((string) ($project->ref_mo ?? '')),
        ];
    }

    /**
     * Empresa de la línea: la de la tarea si es válida para la delegación;
     * si la tarea no tiene, la de la delegación. Devuelve [empresa, error].
     */
    public static function resolveEmpresa(?string $taskEmpresa, string $delegacion): array
    {
        $validas = config("axapta.empresas_validas.$delegacion");
        if (empty($validas)) {
            return [null, "la delegación \"$delegacion\" no tiene empresa configurada para Axapta (config/axapta.php)"];
        }

        $taskEmpresa = strtoupper(trim((string) $taskEmpresa));
        if ($taskEmpresa === '') {
            return [config("axapta.empresa_por_delegacion.$delegacion"), null];
        }

        if (!in_array($taskEmpresa, $validas, true)) {
            return [null, "la empresa $taskEmpresa no es válida para la delegación $delegacion (válidas: " . implode(', ', $validas) . ')'];
        }

        return [$taskEmpresa, null];
    }

    /**
     * Siguiente RegId: nunca por debajo del inicio configurado ni de uno ya usado
     */
    public static function nextRegId(): int
    {
        $start = (int) config('axapta.regid_start', 2000000);
        $max = (int) ExportLedgerLine::max('regid');

        return max($start, $max + 1);
    }

    private static function rejectedBatchIds(): array
    {
        if (self::$rejectedBatchIds === null) {
            self::$rejectedBatchIds = ExportBatch::whereNotNull('rejected_at')->pluck('id')->all();
        }

        return self::$rejectedBatchIds;
    }

    /** Líneas del ledger que cuentan como enviadas (excluye lotes rechazados) */
    private static function validLedger()
    {
        $query = ExportLedgerLine::query();
        $rejected = self::rejectedBatchIds();
        if ($rejected) {
            $query->whereNotIn('batch_id', $rejected);
        }

        return $query;
    }

    /**
     * Puntos de la tarea para Axapta: su puntuación guardada (puntuacion_tarea.cantidad_puntaje: la de la
     * revisión o la automática al pasar a Hecho), igual en todas las tareas del encargo. Si no tiene
     * (p. ej. encargos cerrados antes de existir los puntos automáticos), se calculan al momento con la
     * misma regla (MilestonePointsHelper), sin guardarlos. null si no hay forma de calcularlos (campo vacío).
     */
    private static function taskPoints($taskId): ?float
    {
        if (!$taskId) {
            return null;
        }
        if (!array_key_exists($taskId, self::$pointsCache)) {
            $value = self::$puntuacionByTask !== null
                ? (self::$puntuacionByTask[$taskId] ?? null)
                : PuntuacionTarea::where('id_tarea', $taskId)->value('cantidad_puntaje');
            if ($value === null) {
                // Los puntos automáticos son del encargo (iguales en todas sus tareas): una vez por encargo
                $task = self::task($taskId);
                $milestoneId = $task ? (int) $task->milestone_id : 0;
                if ($milestoneId && !array_key_exists($milestoneId, self::$milestonePointsCache)) {
                    $milestone = self::$milestoneCache[$milestoneId] ?? Milestone::find($milestoneId);
                    self::$milestonePointsCache[$milestoneId] = $milestone ? MilestonePointsHelper::automaticPoints($milestone) : null;
                }
                $value = $milestoneId ? self::$milestonePointsCache[$milestoneId] : null;
            }
            self::$pointsCache[$taskId] = $value !== null ? (float) $value : null;
        }

        return self::$pointsCache[$taskId];
    }

    private static function task($taskId): ?Task
    {
        if (!$taskId) {
            return null;
        }
        if (!array_key_exists($taskId, self::$taskCache)) {
            self::$taskCache[$taskId] = Task::find($taskId);
        }

        return self::$taskCache[$taskId];
    }

    /**
     * Imputado(k, d): segundos del empleado por tarea y día = Σ (h·3600 + m·60 + s) de sus imputaciones.
     * [ "taskId|Y-m-d" => segundos ]. En la app hay un solo registro por tarea, día y persona,
     * así que cada clave es una imputación (como cada línea del fichero original).
     */
    public static function desiredSecondsByTaskDate($milestoneId, $userId): array
    {
        $rows = Timesheet::whereIn('task_id', function ($query) use ($milestoneId) {
            $query->select('id')->from('tasks')->where('milestone_id', $milestoneId);
        })
            ->where('created_by', $userId)
            ->get(['task_id', 'date', 'time']);

        return self::secondsByTaskDate($rows);
    }

    /** Imputado(k, d) a partir de filas de imputación (task_id, date, time) ya cargadas */
    private static function secondsByTaskDate($rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $seconds = self::timeToSeconds($row->time);
            if ($seconds === 0) {
                continue;
            }
            $key = $row->task_id . '|' . date('Y-m-d', strtotime($row->date));
            $map[$key] = ($map[$key] ?? 0) + $seconds;
        }

        return $map;
    }

    /** ¿Alguna imputación con horas? ($byUser: [usuario => filas]) */
    private static function hasHours(array $byUser): bool
    {
        foreach ($byUser as $rows) {
            foreach ($rows as $row) {
                if (self::timeToSeconds($row->time) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Enviado(k, d): segundos ya exportados por tarea y día = Σ round(horas_decimales · 3600) de cada línea
     * (cada línea se redondea al segundo antes de sumar: el ledger guarda 4 decimales y así no se acumula error).
     * Devuelve [segundos por "taskId|Y-m-d", última línea por clave].
     */
    public static function exportedSecondsByTaskDate($milestoneId, string $employeeNumber): array
    {
        $rows = self::validLedger()
            ->where('milestone_id', $milestoneId)
            ->where('employee_number', $employeeNumber)
            ->whereNotNull('work_date')
            ->orderBy('id')
            ->get();

        return self::exportedFromRows($rows);
    }

    /** Enviado(k, d) a partir de líneas del ledger ya cargadas (en orden de id); ignora las sin fecha */
    private static function exportedFromRows($rows): array
    {
        $seconds = [];
        $last = [];
        foreach ($rows as $row) {
            if (empty($row->work_date)) {
                continue;
            }
            $key = $row->task_id . '|' . date('Y-m-d', strtotime($row->work_date));
            $seconds[$key] = ($seconds[$key] ?? 0) + (int) round(((float) $row->hours_decimal) * 3600);
            $last[$key] = $row;
        }

        return [$seconds, $last];
    }

    /** ¿Hay líneas del formato anterior (sin fecha) entre estas líneas del ledger? */
    private static function hasLegacyRows($rows): bool
    {
        foreach ($rows as $row) {
            if (empty($row->work_date)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ¿El encargo tiene horas enviadas con el formato anterior (sin fecha) en lotes no rechazados?
     */
    public static function hasLegacyExport($milestoneId, string $employeeNumber): bool
    {
        return self::validLedger()
            ->where('milestone_id', $milestoneId)
            ->where('employee_number', $employeeNumber)
            ->whereNull('work_date')
            ->exists();
    }

    /**
     * Calcula el export completo SIN escribir nada.
     *
     * @param callable|null $fallbackRef fn(Milestone, Project): string — referencia
     *        automática cuando la tarea no tiene referencia
     * @return array{lines: string[], ledger: array[], skipped: string[], warnings: string[]}
     */
    public static function buildExport(?callable $fallbackRef = null): array
    {
        self::$rejectedBatchIds = null;
        self::$taskCache = [];
        self::$pointsCache = [];
        self::$milestoneCache = [];
        self::$milestonePointsCache = [];
        self::$puntuacionByTask = null;
        self::$businessUnitByProject = null;
        self::$businessUnitByRefMo = null;

        $state = [
            'lines' => [],
            'ledger' => [],
            'skipped' => [],
            'warnings' => [],
            'regId' => self::nextRegId(),
            'lineaPorEncargo' => [],
        ];

        // ---------------------------------------------------------------------------------
        // 1) Todo lo necesario se carga en bloque (unas pocas consultas en total, no por encargo)
        // ---------------------------------------------------------------------------------
        $projects = Project::where('type', 1)->get();
        if ($projects->isEmpty()) {
            return ['lines' => [], 'ledger' => [], 'skipped' => [], 'warnings' => []];
        }
        $projectIds = $projects->pluck('id')->all();
        $workspaces = Workspace::whereIn('id', $projects->pluck('workspace')->filter()->unique()->all())->get()->keyBy('id');

        // Parejas (encargo, empleado) con horas ya enviadas desde la app, por proyecto
        $pairsByProject = [];
        foreach (self::validLedger()
            ->whereIn('project_id', $projectIds)
            ->whereNotNull('work_date')
            ->select('project_id', 'milestone_id', 'employee_number')
            ->distinct()
            ->get() as $pair) {
            $pairsByProject[$pair->project_id][] = $pair;
        }

        // Encargos finalizados de esos proyectos
        $milestonesByProject = [];
        foreach (Milestone::whereIn('project_id', $projectIds)->where('status', 4)->orderBy('id')->get() as $milestone) {
            $milestonesByProject[$milestone->project_id][] = $milestone;
            self::$milestoneCache[(int) $milestone->id] = $milestone;
        }
        $milestoneIds = array_keys(self::$milestoneCache);

        // Responsables de esos encargos
        $users = User::whereIn('id', array_filter(array_map(fn ($m) => (int) $m->milestone_assigned_to_user, self::$milestoneCache)))
            ->get()->keyBy('id');

        // Lo ya enviado (líneas con y sin fecha) de esos encargos, en orden
        $ledgerByMilestone = [];
        if ($milestoneIds) {
            foreach (self::validLedger()->whereIn('milestone_id', $milestoneIds)->orderBy('id')->get() as $row) {
                $ledgerByMilestone[(int) $row->milestone_id][] = $row;
            }
        }

        // Imputaciones de horas de esos encargos: [encargo][usuario] => filas
        $timesheetsByMilestone = [];
        if ($milestoneIds) {
            $rows = Timesheet::join('tasks', 'tasks.id', '=', 'timesheets.task_id')
                ->whereIn('tasks.milestone_id', $milestoneIds)
                ->get(['timesheets.task_id', 'timesheets.date', 'timesheets.time', 'timesheets.created_by', 'tasks.milestone_id']);
            foreach ($rows as $row) {
                $timesheetsByMilestone[(int) $row->milestone_id][(int) $row->created_by][] = $row;
            }
        }

        // Tareas y su puntuación
        if ($milestoneIds) {
            foreach (Task::whereIn('milestone_id', $milestoneIds)->get() as $task) {
                self::$taskCache[$task->id] = $task;
            }
        }
        self::$puntuacionByTask = [];
        if (self::$taskCache) {
            foreach (PuntuacionTarea::whereIn('id_tarea', array_keys(self::$taskCache))->get(['id_tarea', 'cantidad_puntaje']) as $p) {
                if ($p->cantidad_puntaje !== null && !isset(self::$puntuacionByTask[$p->id_tarea])) {
                    self::$puntuacionByTask[$p->id_tarea] = $p->cantidad_puntaje;
                }
            }
        }

        // Puntos automáticos de cada encargo (para las tareas sin puntuación): misma fórmula que
        // MilestonePointsHelper::automaticPoints, con las horas y tareas ya cargadas
        $typePuntuacion = TaskType::where('project_type', 1)->pluck('puntuacion', 'id')->all();
        $typesByMilestone = [];
        foreach (self::$taskCache as $task) {
            if (!empty($task->type_id)) {
                $typesByMilestone[(int) $task->milestone_id][(int) $task->type_id] = true;
            }
        }
        foreach ($milestoneIds as $mid) {
            $seconds = 0;
            foreach ($timesheetsByMilestone[$mid] ?? [] as $rows) {
                foreach ($rows as $row) {
                    $seconds += self::timeToSeconds($row->time);
                }
            }
            $typePoints = 0.0;
            foreach (array_keys($typesByMilestone[$mid] ?? []) as $typeId) {
                $typePoints += (float) ($typePuntuacion[$typeId] ?? 0);
            }
            self::$milestonePointsCache[$mid] = MilestonePointsHelper::pointsFrom($seconds, $typePoints);
        }

        // Delegación desde la master obra (solo hace falta en proyectos sin ref_delegation)
        self::$businessUnitByProject = [];
        self::$businessUnitByRefMo = [];
        $noDelegation = $projects->filter(fn ($p) => trim((string) ($p->ref_delegation ?? '')) === '');
        if ($noDelegation->isNotEmpty()) {
            foreach (MasterObra::whereIn('project_id', $noDelegation->pluck('id')->all())
                ->whereNotNull('business_unit')->where('business_unit', '!=', '')
                ->orderBy('id')->get(['project_id', 'business_unit']) as $mo) {
                self::$businessUnitByProject[$mo->project_id] ??= $mo->business_unit;
            }
            $refMos = $noDelegation->pluck('ref_mo')->filter()->unique()->values()->all();
            if ($refMos) {
                foreach (MasterObra::whereIn('ref_mo', $refMos)
                    ->whereNotNull('business_unit')->where('business_unit', '!=', '')
                    ->orderBy('id')->get(['ref_mo', 'business_unit']) as $mo) {
                    self::$businessUnitByRefMo[$mo->ref_mo] ??= $mo->business_unit;
                }
            }
        }

        // ---------------------------------------------------------------------------------
        // 2) Cálculo, igual que antes pero con los datos ya cargados
        // ---------------------------------------------------------------------------------
        foreach ($projects as $project) {
            $projectData = self::getProjectExportData($project, $workspaces[$project->workspace] ?? null);
            $processed = [];

            foreach ($milestonesByProject[$project->id] ?? [] as $milestone) {
                $label = "{$project->name} / {$milestone->title}";
                $milestoneTimesheets = $timesheetsByMilestone[(int) $milestone->id] ?? [];

                $user = $milestone->milestone_assigned_to_user ? ($users[(int) $milestone->milestone_assigned_to_user] ?? null) : null;
                if (!$user) {
                    // Solo se avisa si el encargo tiene horas que habría que enviar
                    if (self::hasHours($milestoneTimesheets)) {
                        self::skip($state, $label, 'el encargo no tiene empleado asignado');
                    }
                    continue;
                }

                $desired = self::secondsByTaskDate($milestoneTimesheets[(int) $user->id] ?? []);

                $employeeNumber = trim((string) $user->number_employee);
                if ($employeeNumber === '') {
                    if ($desired) {
                        self::skip($state, $label, "{$user->name} no tiene número de empleado");
                    }
                    continue;
                }
                $processed[$milestone->id . '|' . $employeeNumber] = true;

                $ledgerRows = array_values(array_filter(
                    $ledgerByMilestone[(int) $milestone->id] ?? [],
                    fn ($row) => (string) $row->employee_number === $employeeNumber
                ));

                if (self::hasLegacyRows($ledgerRows)) {
                    self::skip($state, $label, 'tiene horas enviadas con el formato anterior; marca ese lote como rechazado (php artisan axapta:reject-batch) para reenviarlas');
                    continue;
                }

                [$exported, $lastRows] = self::exportedFromRows($ledgerRows);

                self::appendDeltaLines($state, $project, $projectData, $milestone, $employeeNumber, $desired, $exported, $lastRows, $fallbackRef, $label);
            }

            // Reversiones: lo enviado de encargos borrados o de empleados que ya no tienen horas en el encargo.
            // Un encargo que ya no está finalizado no se revierte: lo enviado queda en Axapta.
            // Son casos raros: se consultan uno a uno.
            foreach ($pairsByProject[$project->id] ?? [] as $pair) {
                if (empty($pair->milestone_id) || isset($processed[$pair->milestone_id . '|' . $pair->employee_number])) {
                    continue;
                }

                $milestone = self::$milestoneCache[(int) $pair->milestone_id] ?? Milestone::find($pair->milestone_id);
                if ($milestone && (int) $milestone->status !== 4) {
                    continue;
                }

                if ($milestone) {
                    $user = User::where('number_employee', $pair->employee_number)->first();
                    $stillHasHours = $user && Timesheet::whereIn('task_id', function ($query) use ($milestone) {
                        $query->select('id')->from('tasks')->where('milestone_id', $milestone->id);
                    })->where('created_by', $user->id)->exists();
                    if ($stillHasHours) {
                        continue;
                    }
                }

                [$exported, $lastRows] = self::exportedSecondsByTaskDate($pair->milestone_id, $pair->employee_number);
                $label = $project->name . ' / ' . ($milestone->title ?? "encargo {$pair->milestone_id} (borrado)");

                self::appendDeltaLines($state, $project, $projectData, $milestone, $pair->employee_number, [], $exported, $lastRows, $fallbackRef, $label);
            }
        }

        // Fuera de buildExport() las funciones públicas vuelven a consultar la base de datos
        self::$puntuacionByTask = null;
        self::$businessUnitByProject = null;
        self::$businessUnitByRefMo = null;

        return [
            'lines' => $state['lines'],
            'ledger' => $state['ledger'],
            'skipped' => array_values(array_unique($state['skipped'])),
            'warnings' => array_values(array_unique($state['warnings'])),
        ];
    }

    private static function skip(array &$state, string $label, string $reason): void
    {
        $state['skipped'][] = "$label: $reason";
    }

    /**
     * Una línea por cada tarea y día cuya cantidad difiere de lo ya enviado
     */
    private static function appendDeltaLines(array &$state, $project, $projectData, ?Milestone $milestone, string $employeeNumber,
                                             array $desired, array $exported, array $lastRows, ?callable $fallbackRef, string $label): void
    {
        $keys = array_unique(array_merge(array_keys($desired), array_keys($exported)));
        // Orden: fecha y después tarea
        usort($keys, function ($a, $b) {
            [$ta, $da] = explode('|', $a);
            [$tb, $db] = explode('|', $b);

            return [$da, (int) $ta] <=> [$db, (int) $tb];
        });

        $descripcion = $milestone ? (string) $milestone->summary : '';
        $milestoneId = $milestone->id ?? null;

        foreach ($keys as $key) {
            // Δ(k, d) = Imputado(k, d) − Enviado(k, d), en segundos; sin diferencia no hay línea
            $deltaSeconds = ($desired[$key] ?? 0) - ($exported[$key] ?? 0);
            if ($deltaSeconds === 0) {
                continue;
            }

            [$taskId, $workDate] = explode('|', $key);
            $task = self::task($taskId);
            $previous = $lastRows[$key] ?? null;
            $dayLabel = "$label, " . date('d/m/Y', strtotime($workDate));

            // Una corrección negativa repite los datos con los que se envió la línea original
            if ($deltaSeconds < 0 && $previous) {
                $delegacion = (string) $previous->delegacion;
                $empresa = (string) $previous->empresa;
                $masterobrasid = (string) $previous->masterobrasid;
                $ref = (string) $previous->ref;
                $puntos = (float) $previous->puntos > 0 ? (float) $previous->puntos : null;
            } else {
                $delegacion = $projectData->delegacion;
                $masterobrasid = $projectData->masterobrasid;
                // Puntos: la puntuación de la tarea (la misma en todas las tareas del encargo, se calcula al revisarlo)
                $puntos = self::taskPoints($taskId);

                [$empresa, $error] = self::resolveEmpresa($task->empresa ?? null, $delegacion);
                if ($error) {
                    self::skip($state, $label, $error);
                    continue;
                }

                $ref = self::axaptaReference($task->referencia ?? '', $delegacion);
                if ($ref === '' && $milestone && $fallbackRef) {
                    $ref = self::axaptaReference($fallbackRef($milestone, $project), $delegacion);
                    if ($ref !== '') {
                        $state['warnings'][] = "$label: la tarea \"" . (trim((string) ($task->description ?? '')) !== '' ? Str::limit(trim($task->description), 40) : "#$taskId") . "\" no tiene referencia; se usa la automática $ref";
                    }
                }
                if (strlen($ref) > 14) {
                    $state['warnings'][] = "$label: la referencia $ref se recorta a 14 caracteres (" . substr($ref, 0, 14) . ')';
                    $ref = substr($ref, 0, 14);
                }
            }

            if ($masterobrasid === '') {
                self::skip($state, $label, 'el proyecto no tiene Master Obra (ref_mo)');
                continue;
            }
            if ($ref === '') {
                self::skip($state, $dayLabel, 'no hay referencia para la línea');
                continue;
            }

            $state['lineaPorEncargo'][$milestoneId] = ($state['lineaPorEncargo'][$milestoneId] ?? 0) + 1;
            $regId = $state['regId']++;
            $hours = round($deltaSeconds / 3600, 4);

            $state['lines'][] = self::formatLine([
                'regId' => $regId,
                'fecha' => date('Ymd', strtotime($workDate)),
                'empresa' => $empresa,
                'delegacion' => $delegacion,
                'empleado' => $employeeNumber,
                'masterobrasid' => $masterobrasid,
                'obra' => '',
                'descripcion' => $descripcion,
                'op' => config('axapta.op', '210'),
                'horas' => self::secondsToTimeFormat($deltaSeconds),
                'ref' => $ref,
                'linea' => $state['lineaPorEncargo'][$milestoneId],
                'hrDecimal' => number_format($deltaSeconds / 3600, 7, '.', ''), // h + m/60 + s/3600
                'puntos' => $puntos !== null ? number_format($puntos, 2, '.', '') : '',
            ]);

            $state['ledger'][] = [
                'project_id' => $project->id,
                'milestone_id' => $milestoneId,
                'task_id' => (int) $taskId ?: null,
                'work_date' => $workDate,
                'regid' => $regId,
                'employee_number' => $employeeNumber,
                'empresa' => $empresa,
                'delegacion' => $delegacion,
                'masterobrasid' => $masterobrasid,
                'ref' => $ref,
                'op' => config('axapta.op', '210'),
                'hours_decimal' => $hours,
                'puntos' => $puntos !== null ? round($puntos, 4) : 0,
                'hr_decimal' => $hours,
                'created_at' => now(),
            ];
        }
    }
}
