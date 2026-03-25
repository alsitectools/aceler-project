<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\select;

class Milestone extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'assign_to',
        'status',
        'order',
        'company',
        'contractor',
        'contractorAdress',
        'jobsiteAdress',
        'start_date', //creation_date
        'end_date', //estimated_date
        'task_start_date',
        'finalization_date',
        'summary',
        'reminder_mail_is_send',
        'milestone_assigned_to_user',
        'priority',
        'is_waiting'
    ];

    public function daysLeft()
    {

        return  round((strtotime($this->end_date) - strtotime(date('Y-m-d'))) /   24 / 60 / 60,);
    }

    function showMilestonetasks()
    {
        return Task::with(['type', 'user', 'timesheets'])
            ->where('tasks.milestone_id', $this->id)
            ->get();
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function userTasks($userId)
    {
        return $this->hasMany(Task::class)->where('assign_to', $userId);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function tasksWithinDateRange($startDate, $estimatedDate)
    {
        return $this->hasMany(Task::class, 'milestone_id', 'id')
            ->where(function ($query) use ($startDate, $estimatedDate) {
                $query->where('start_date', '>=', $startDate)
                    ->where('estimated_date', '<=', $estimatedDate);
            });
    }

    public function salesManager()
    {
        $sales_manager = User::join('milestones', 'milestones.assign_to', '=', 'users.id')
            ->where('milestones.assign_to', $this->assign_to)
            ->select('users.*')
            ->first();

        return $sales_manager ? $sales_manager : "Unknow";
    }

    public function getRequestedBy()
    {
        $requested_by = User::join('milestones', 'milestones.assign_to', '=', 'users.id')
            ->where('milestones.assign_to', $this->assign_to)
            ->first();

        return $requested_by ? $requested_by : null;
    }
    public function getAssignedToUser()
    {
        $assigned_to_user = User::join('milestones', 'milestones.milestone_assigned_to_user', '=', 'users.id')
            ->where('milestones.milestone_assigned_to_user', $this->milestone_assigned_to_user)
            ->first();

        return $assigned_to_user ? $assigned_to_user : null;
    }

    public function milestone()
    {
        $milestone = Milestone::join('tasks', 'milestones.id', '=', 'tasks.milestone_id')
            ->where('milestone_id', $this->id)->first();

        return $milestone ? $milestone->title : null;
    }
    // Relación con los archivos
    public function files()
    {
        return $this->hasMany(MilestoneFile::class);
    }

    // una milestone puede tener fase si el proyecto es type 3/5
    public function phase()
    {
        return $this->hasOne(MilestonePhases::class, 'id_milestone', 'id');
    }

    // Relación con múltiples fases (hasMany)
    public function phases()
    {
        return $this->hasMany(MilestonePhases::class, 'id_milestone', 'id');
    }

    // una milestone puede tener fase si el proyecto es type 3/5
    public function stage()
    {
        return $this->hasOne(MilestoneStages::class, 'id_milestone', 'id');
    }

    // Relación con múltiples fases (hasMany)
    public function stages()
    {
        return $this->hasMany(MilestoneStages::class, 'id_milestone', 'id');
    }

    public function getResolvedStageNameAttribute(): ?string
    {
        $stage = $this->relationLoaded('stage')
            ? $this->stage
            : $this->stage()->with('stageProject')->first();

        if (!$stage) {
            return null;
        }

        if (!empty($stage->milestone_stage_project_id)) {
            $stageProjectName = $stage->relationLoaded('stageProject')
                ? optional($stage->stageProject)->name
                : MilestoneStageProject::where('id', $stage->milestone_stage_project_id)->value('name');

            if (!empty($stageProjectName)) {
                return trim((string) $stageProjectName);
            }
        }

        $stageName = trim((string) ($stage->stages ?? ''));

        return $stageName !== '' ? $stageName : null;
    }


}
