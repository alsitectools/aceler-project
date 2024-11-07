<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = ['name', 'color', 'complete', 'order'];

    public static function getTaskCountsForDate($projectId, $date)
    // public static function getMilestoneCountsForDate($workspaceId, $projectId, $date)
    {
        return static::leftJoin('milestones', 'stages.id', '=', 'milestones.status')
            ->whereDate('milestones.updated_at', $date)
            ->when($projectId !== null, function ($query) use ($projectId) {
                return $query->where('milestones.project_id', $projectId);
            })
            ->groupBy('stages.id')
            ->select('stages.id', \DB::raw('count(milestones.id) as milestones_count'))
            ->get()
            ->pluck('milestones_count', 'id')
            ->toArray();

        // return static::leftJoin('tasks', 'stages.id', '=', 'tasks.project_id')
        //     ->where('stages.workspace_id', $workspaceId)
        //     ->whereDate('tasks.updated_at', $date)
        //     ->when($projectId !== null, function ($query) use ($projectId) {
        //         return $query->where('tasks.project_id', $projectId);
        //     })
        //     ->groupBy('stages.id')
        //     ->select('stages.id', \DB::raw('count(tasks.id) as task_count'))
        //     ->get()
        //     ->pluck('task_count', 'id')
        //     ->toArray();
    }
}
