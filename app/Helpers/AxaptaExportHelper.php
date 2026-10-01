<?php

namespace App\Helpers;

use App\Models\Delegation;
use App\Models\ExportLedgerLine;
use App\Models\Milestone;
use App\Models\Project;
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
     * Calcula el estado DESEADO ACTUAL (desired_actual) de UN ENCARGO
     * Suma de horas de los timesheets imputados por el empleado asignado
     * en las tareas del encargo. Los puntos y hr_decimal siempre son 0.
     *
     * @param int $milestoneId
     * @param int $userId Empleado asignado al encargo (milestone_assigned_to_user)
     * @return object
     */
    public static function calculateDesiredState($milestoneId, $userId): object
    {
        $timesheets = Timesheet::whereIn('task_id', function ($query) use ($milestoneId) {
            $query->select('id')->from('tasks')->where('milestone_id', $milestoneId);
        })
            ->where('created_by', $userId)
            ->get();

        $totalHours = 0;

        foreach ($timesheets as $timesheet) {
            $totalHours += self::timeToDecimal($timesheet->time);
        }

        return (object)[
            'hours_decimal' => $totalHours,
            'puntos' => 0,
            'hr_decimal' => 0
        ];
    }

    /**
     * Calcula el estado EXPORTADO ACUMULADO (exported_acumulado) de UN ENCARGO
     * Suma de valores históricos en export_ledger_lines para ese encargo y empleado
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

        return (object)[
            'hours_decimal' => (float)$totalHours,
            'puntos' => 0,
            'hr_decimal' => 0
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
     * Detecta encargos que fueron borrados o quedaron sin timesheets (del empleado asignado)
     * (existían en ledger por encargo pero ya no tienen horas en BD)
     * Retorna array de combinaciones (project_id, milestone_id, employee_number) borradas
     *
     * Nota: un encargo que sale de la fase 4 (Hecho) NO se revuelve. Como ya fue
     * exportado, sus horas quedan registradas en Axapta tal como se enviaron.
     * Solo se revierte si el encargo o el usuario fueron borrados, o si el empleado
     * quedó sin timesheets en el encargo.
     *
     * @param int $projectId
     * @return array
     */
    public static function detectDeletedRecords($projectId): array
    {
        // Obtener todos los encargos que alguna vez fueron exportados para este proyecto
        $exportedMilestones = ExportLedgerLine::where('project_id', $projectId)
            ->select('milestone_id', 'employee_number')
            ->distinct()
            ->get();

        $deletedRecords = [];

        foreach ($exportedMilestones as $record) {
            if (empty($record->milestone_id)) {
                continue;
            }

            $milestoneId = $record->milestone_id;
            $employeeNumber = $record->employee_number;

            // Verificar si el encargo aún existe y sigue en estado Hecho (4)
            $milestone = Milestone::find($milestoneId);

            if (!$milestone) {
                // Encargo fue borrado
                $deletedRecords[] = [
                    'project_id' => $projectId,
                    'milestone_id' => $milestoneId,
                    'employee_number' => $employeeNumber,
                    'reason' => 'MILESTONE_DELETED'
                ];
                continue;
            }

            if ((int) $milestone->status !== 4) {
                // El encargo ya no está en Hecho, pero ya fue exportado.
                // No se revierte: las horas enviadas a Axapta se conservan.
                continue;
            }

            // Verificar si el empleado asignado actual coincide con el exportado
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

            // Verificar si hay timesheets actuales del empleado asignado en el encargo
            $hasCurrentTimesheets = Timesheet::whereIn('task_id', function ($query) use ($milestoneId) {
                $query->select('id')->from('tasks')->where('milestone_id', $milestoneId);
            })
                ->where('created_by', $user->id)
                ->exists();

            if (!$hasCurrentTimesheets) {
                // Este empleado ya no tiene timesheets en este encargo
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
        $delegationId = $project->ref_delegation ?? $workspace?->delegation_id ?? '0';

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
