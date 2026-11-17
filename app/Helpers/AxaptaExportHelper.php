<?php

namespace App\Helpers;

use App\Models\ExportBatch;
use App\Models\ExportLedgerLine;
use App\Models\MasterObra;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\PuntuacionTarea;
use App\Models\Task;
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

        $businessUnit = trim((string) ($businessUnit ?? ''));

        return $businessUnit !== '' ? $businessUnit : null;
    }

    /**
     * Obtiene datos del proyecto y workspace para la exportación
     */
    public static function getProjectExportData($project)
    {
        $workspace = Workspace::find($project->workspace);
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
            $value = PuntuacionTarea::where('id_tarea', $taskId)->value('cantidad_puntaje');
            if ($value === null) {
                $task = self::task($taskId);
                $milestone = $task ? Milestone::find($task->milestone_id) : null;
                $value = $milestone ? MilestonePointsHelper::automaticPoints($milestone) : null;
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

        $seconds = [];
        $last = [];
        foreach ($rows as $row) {
            $key = $row->task_id . '|' . date('Y-m-d', strtotime($row->work_date));
            $seconds[$key] = ($seconds[$key] ?? 0) + (int) round(((float) $row->hours_decimal) * 3600);
            $last[$key] = $row;
        }

        return [$seconds, $last];
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

        $state = [
            'lines' => [],
            'ledger' => [],
            'skipped' => [],
            'warnings' => [],
            'regId' => self::nextRegId(),
            'lineaPorEncargo' => [],
        ];

        foreach (Project::where('type', 1)->get() as $project) {
            $projectData = self::getProjectExportData($project);
            $processed = [];

            foreach (Milestone::where('project_id', $project->id)->where('status', 4)->get() as $milestone) {
                $label = "{$project->name} / {$milestone->title}";

                $user = $milestone->milestone_assigned_to_user ? User::find($milestone->milestone_assigned_to_user) : null;
                if (!$user) {
                    self::skip($state, $label, 'el encargo no tiene empleado asignado');
                    continue;
                }

                $employeeNumber = trim((string) $user->number_employee);
                if ($employeeNumber === '') {
                    self::skip($state, $label, "{$user->name} no tiene número de empleado");
                    continue;
                }
                $processed[$milestone->id . '|' . $employeeNumber] = true;

                if (self::hasLegacyExport($milestone->id, $employeeNumber)) {
                    self::skip($state, $label, 'tiene horas enviadas con el formato anterior; marca ese lote como rechazado (php artisan axapta:reject-batch) para reenviarlas');
                    continue;
                }

                [$exported, $lastRows] = self::exportedSecondsByTaskDate($milestone->id, $employeeNumber);
                $desired = self::desiredSecondsByTaskDate($milestone->id, $user->id);

                self::appendDeltaLines($state, $project, $projectData, $milestone, $employeeNumber, $desired, $exported, $lastRows, $fallbackRef, $label);
            }

            // Reversiones: lo enviado de encargos borrados o de empleados que ya no tienen horas en el encargo.
            // Un encargo que ya no está finalizado no se revierte: lo enviado queda en Axapta.
            $pairs = self::validLedger()
                ->where('project_id', $project->id)
                ->whereNotNull('work_date')
                ->select('milestone_id', 'employee_number')
                ->distinct()
                ->get();

            foreach ($pairs as $pair) {
                if (empty($pair->milestone_id) || isset($processed[$pair->milestone_id . '|' . $pair->employee_number])) {
                    continue;
                }

                $milestone = Milestone::find($pair->milestone_id);
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
