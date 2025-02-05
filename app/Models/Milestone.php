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
        'start_date',
        'end_date',
        'summary'
    ];

    public function daysLeft()
    {

        return  round((strtotime($this->end_date) - strtotime(date('Y-m-d'))) /   24 / 60 / 60,);
    }

    // function tasks()
    // {
    //     return Task::join('task_types', 'task_types.id', 'tasks.type_id')
    //         ->where('tasks.milestone_id', $this->id)->pluck('task_types.name');
    // }
    // public function tasks()
    // {
    //     return Task::join('task_types', 'task_types.id', '=', 'tasks.type_id')
    //         ->where('tasks.milestone_id', $this->id)
    //         ->select('tasks.*', 'task_types.name as task_name')
    //         ->get();
    // }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    // public function tasks()
    // {
    //     return $this->hasMany(Task::class, 'milestone_id', 'id');
    // }
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
    public function taskStart()
    {
        $milestoneIds = Milestone::where('project_id', function ($query) {
            $query->select('project_id')
                ->from('milestones')
                ->where('id', $this->id);
        })->pluck('id');

        $firstTask = Task::whereIn('milestone_id', $milestoneIds)
            ->orderBy('start_date', 'asc')
            ->first();

        return $firstTask ? Carbon::parse($firstTask->start_date)->format('d-m-Y') : '...';
    }

    public function tasksEnd()
    {
        $milestoneIds = Milestone::where('project_id', function ($query) {
            $query->select('project_id')
                ->from('milestones')
                ->where('id', $this->id);
        })
            ->pluck('id');

        $lastTask = Task::whereIn('milestone_id', $milestoneIds)
            ->orderBy('end_date', 'desc')
            ->first();

        return $lastTask->end_date ? Carbon::parse($lastTask->end_date)->format('d-m-Y') : '...';
    }

    public function salesManager()
    {

        $sales_manager = User::join('milestones', 'milestones.assign_to', '=', 'users.id')
            ->where('milestones.assign_to', $this->assign_to)
            ->select('users.*')
            ->first();


        return $sales_manager ? $sales_manager : "Unknow";
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
