<?php

namespace App\Helpers;

use App\Models\Delegation;
use App\Models\ExportLedgerLine;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\PuntuacionTarea;
use App\Models\Task;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class AxaptaExportHelper
{
    /**
     * Convierte formato HH:MM a horas decimales
     */
    public static function timeToDecimal($timeValue): float
    {
        if (!$timeValue || strpos($timeValue, ':') === false) {
            return 0;
        }

        $parts = explode(':', $timeValue);
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);

        return $hours + ($minutes / 60);
    }

    /**
     * Convierte horas decimales a formato HH:MM:SS
     * Soporta valores negativos
     */
    public static function decimalToTimeFormat(float $decimalHours): string
    {
        $isNegative = $decimalHours < 0;
        $absHours = abs($decimalHours);

        $hours = (int)$absHours;
        $minutesDecimal = ($absHours - $hours) * 60;
        $minutes = (int)$minutesDecimal;
        $seconds = (int)(($minutesDecimal - $minutes) * 60);

        $timeStr = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return $isNegative ? '-' . $timeStr : $timeStr;
    }

    /**
     * Divide horas si exceden 99:00:00 (máximo permitido por Axapta)
     * Retorna array de objetos con hours_decimal, puntos, hr_decimal divididos proporcionalmente
     *
     * @param float $hours_decimal
     * @param float $puntos
     * @param float $hr_decimal
     * @return array
     */
    public static function splitHoursIfNeeded(float $hours_decimal, float $puntos, float $hr_decimal): array
    {
        $maxHours = 99; // Máximo permitido
        $absHours = abs($hours_decimal);

        if ($absHours <= $maxHours) {
            return [
                (object)[
                    'hours_decimal' => $hours_decimal,
                    'puntos' => $puntos,
                    'hr_decimal' => $hr_decimal
                ]
            ];
        }

        // Necesita división
        $lines = [];
        $isNegative = $hours_decimal < 0;
        $factor = $isNegative ? -1 : 1;

        $remaining = $absHours;
        $lineCount = 0;

        while ($remaining > 0) {
            $lineCount++;
            $currentHours = min($maxHours, $remaining);
            $proportion = $currentHours / $absHours;

            $lines[] = (object)[
                'hours_decimal' => $currentHours * $factor,
                'puntos' => $puntos * $proportion,
                'hr_decimal' => $hr_decimal * $proportion
            ];

            $remaining -= $currentHours;
        }

        // Ajuste en la última línea para cuadrar exactamente
        if (!empty($lines)) {
            $sumPreviousLines = array_sum(
                array_map(fn($l) => abs($l->hours_decimal), array_slice($lines, 0, -1))
            );
            $lastLine = &$lines[count($lines) - 1];
            $lastLine->hours_decimal = $absHours - $sumPreviousLines;
            if ($isNegative) {
                $lastLine->hours_decimal *= -1;
            }
        }

        return $lines;
    }

    /**
     * Calcula el estado DESEADO ACTUAL (desired_actual)
     * Suma de horas/puntos de timesheets que existen ahora mismo
     *
     * @param int $projectId
     * @param int $milestoneId
     * @param int $userId
     * @return object
     */
    public static function calculateDesiredState($projectId, $milestoneId, $userId): object
    {
        $tasks = Task::where('milestone_id', $milestoneId)->get();

        $totalHours = 0;
        $totalPuntos = 0;
        $totalHrDecimal = 0;

        foreach ($tasks as $task) {
            // Obtener timesheets de este usuario en esta tarea
            $timesheets = Timesheet::where('task_id', $task->id)
                ->where('created_by', $userId)
                ->get();

            if ($timesheets->count() > 0) {
                // Obtener puntuación tarea (solo una vez por tarea)
                $puntuacion = PuntuacionTarea::where('id_tarea', $task->id)->first();
                $cantidadPuntajeTarea = (float)($puntuacion->cantidad_puntaje ?? 0);
                $puntuacionHora = (float)($puntuacion->puntos_hora ?? 0);

                $totalPuntos += $cantidadPuntajeTarea;
                $totalHrDecimal += $puntuacionHora;

                // Sumar horas de timesheets
                foreach ($timesheets as $timesheet) {
                    $totalHours += self::timeToDecimal($timesheet->time);
                }
            }
        }

        return (object)[
            'hours_decimal' => $totalHours,
            'puntos' => $totalPuntos,
            'hr_decimal' => $totalHrDecimal
        ];
    }

    /**
     * Calcula el estado EXPORTADO ACUMULADO (exported_acumulado)
     * Suma de valores históricos en export_ledger_lines
     *
     * @param int $projectId
     * @param int $milestoneId
     * @param User $user
     * @return object
     */
    public static function calculateExportedState($projectId, $milestoneId, User $user): object
    {
        $exported = ExportLedgerLine::where('project_id', $projectId)
            ->where('milestone_id', $milestoneId)
            ->where('employee_number', $user->number_employee ?? '')
            ->get();

        $totalHours = $exported->sum('hours_decimal');
        $totalPuntos = $exported->sum('puntos');
        $totalHrDecimal = $exported->sum('hr_decimal');

        return (object)[
            'hours_decimal' => (float)$totalHours,
            'puntos' => (float)$totalPuntos,
            'hr_decimal' => (float)$totalHrDecimal
        ];
    }

    /**
     * Calcula el DELTA (cambios que se deben exportar)
     *
     * @param object $desired
     * @param object $exported
     * @return object
     */
    public static function calculateDelta($desired, $exported): object
    {
        return (object)[
            'hours_decimal' => $desired->hours_decimal - $exported->hours_decimal,
            'puntos' => $desired->puntos - $exported->puntos,
            'hr_decimal' => $desired->hr_decimal - $exported->hr_decimal
        ];
    }

    /**
     * Detecta registros que fueron borrados (existían en ledger pero ya no existen en BD)
     * Retorna array de combinaciones (project_id, milestone_id, employee_number) borradas
     *
     * @param int $projectId
     * @return array
     */
    public static function detectDeletedRecords($projectId): array
    {
        // Obtener todas las milestones que alguna vez fueron exportadas para este proyecto
        $exportedMilestones = ExportLedgerLine::where('project_id', $projectId)
            ->select('milestone_id', 'employee_number')
            ->distinct()
            ->get();

        $deletedRecords = [];

        foreach ($exportedMilestones as $record) {
            $milestoneId = $record->milestone_id;
            $employeeNumber = $record->employee_number;

            // Verificar si la milestone aún existe
            $milestone = Milestone::find($milestoneId);

            if (!$milestone) {
                // Milestone fue borrada
                $deletedRecords[] = [
                    'project_id' => $projectId,
                    'milestone_id' => $milestoneId,
                    'employee_number' => $employeeNumber,
                    'reason' => 'MILESTONE_DELETED'
                ];
                continue;
            }

            // Verificar si existen timesheets actuales para este usuario en esta milestone
            $user = User::where('number_employee', $employeeNumber)->first();
            if (!$user) {
                // Usuario no existe
                $deletedRecords[] = [
                    'project_id' => $projectId,
                    'milestone_id' => $milestoneId,
                    'employee_number' => $employeeNumber,
                    'reason' => 'USER_DELETED'
                ];
                continue;
            }

            // Verificar si hay timesheets actuales
            $tasks = Task::where('milestone_id', $milestoneId)->pluck('id');
            $hasCurrentTimesheets = Timesheet::whereIn('task_id', $tasks)
                ->where('created_by', $user->id)
                ->exists();

            if (!$hasCurrentTimesheets) {
                // Este usuario ya no tiene timesheets en esta milestone
                // Pero podría ser que todas sus tareas fueron borradas
                $deletedRecords[] = [
                    'project_id' => $projectId,
                    'milestone_id' => $milestoneId,
                    'employee_number' => $employeeNumber,
                    'reason' => 'NO_TIMESHEETS'
                ];
            }
        }

        return $deletedRecords;
    }

    /**
     * Obtiene datos del proyecto y workspace para la exportación
     */
    public static function getProjectExportData($project)
    {
        $workspace = Workspace::find($project->workspace);
        $delegationId = $workspace?->delegation_id ?? '0';

        $delegation = Delegation::find($delegationId);
        $empresa = $delegation?->empresa ?? '';

        return (object)[
            'delegacion' => $delegationId,
            'empresa' => $empresa,
            'masterobrasid' => $project->ref_mo ?? '',
            'ref' => $delegationId . '-' . $project->id
        ];
    }

    /**
     * Valida si hay cambios significativos (delta ≠ 0)
     */
    public static function hasDelta($delta): bool
    {
        return abs($delta->hours_decimal) > 0.001 ||
               abs($delta->puntos) > 0.001 ||
               abs($delta->hr_decimal) > 0.001;
    }
}
