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
        return Task::join('task_types', 'task_types.id', 'tasks.type_id')
            ->where('tasks.milestone_id', $this->id)->pluck('task_types.name');
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
}
