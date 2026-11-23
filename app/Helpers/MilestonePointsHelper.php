<?php

namespace App\Helpers;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\PuntuacionTarea;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\Timesheet;
use Illuminate\Support\Facades\DB;

/**
 * Puntos automáticos de los encargos de obra (opción B).
 *
 * Los encargos con tarea Drawing que pasan por el formulario de revisión tienen su propio cálculo
 * (ProjectController::milestoneReviewSubmit). El resto de encargos de obra con horas imputadas
 * reciben puntos automáticos:
 *
 *   Puntos = horas imputadas del encargo (todas sus tareas y personas, h + m/60 + s/3600)
 *          + Σ puntos fijos de los tipos de tarea del encargo (task_types.puntuacion, cada tipo una vez)
 *
 * Como en la revisión, la puntuación es la misma en todas las tareas del encargo (puntuacion_tarea).
 */
class MilestonePointsHelper
{
    /** Puntos automáticos del encargo, o null si no es de obra o no tiene horas */
    public static function automaticPoints(Milestone $milestone): ?float
    {
        $project = Project::find($milestone->project_id);
        if (!$project || (int) $project->type !== 1) {
            return null;
        }

        $taskIds = Task::where('milestone_id', $milestone->id)->pluck('id');
        if ($taskIds->isEmpty()) {
            return null;
        }

        $seconds = (int) Timesheet::whereIn('task_id', $taskIds)->sum(DB::raw('TIME_TO_SEC(time)'));
        if ($seconds <= 0) {
            return null;
        }

        $typePoints = (float) TaskType::where('project_type', 1)
            ->whereIn('id', Task::where('milestone_id', $milestone->id)->pluck('type_id')->filter()->unique())
            ->sum('puntuacion');

        return round($seconds / 3600 + $typePoints, 2);
    }

    /** ¿Alguna tarea del encargo tiene ya puntuación (de la revisión o automática)? */
    public static function hasPoints(Milestone $milestone): bool
    {
        return PuntuacionTarea::whereIn('id_tarea', Task::where('milestone_id', $milestone->id)->pluck('id'))->exists();
    }

    /**
     * Guarda los puntos automáticos en todas las tareas del encargo si aún no tiene puntuación
     * (no pisa la de la revisión). Se llama al pasar el encargo a Hecho.
     */
    public static function ensureAutomatic(Milestone $milestone): void
    {
        if (self::hasPoints($milestone)) {
            return;
        }

        $points = self::automaticPoints($milestone);
        if ($points === null) {
            return;
        }

        foreach (Task::where('milestone_id', $milestone->id)->get(['id', 'assign_to']) as $task) {
            PuntuacionTarea::updateOrCreate(
                ['id_tarea' => $task->id],
                [
                    'cantidad_puntaje' => $points,
                    'user_id' => $task->assign_to,
                    'puntos_hora' => null,
                ]
            );
        }
    }
}
