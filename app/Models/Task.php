<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'milestone_id',
        'type_id',
        'assign_to',
        'start_date',
        'estimated_date',
    ];
    // use for invoice details
    public function invoiceproject()
    {
        return $this->belongsTo('App\Models\Project', 'project_id', 'id');
    }
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'milestone_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    // En el modelo Task.php
    public function scopeWithinDateRange($query, $firstDay, $seventhDay)
    {
        return $query->where(function ($q) use ($firstDay, $seventhDay) {
            $q->whereBetween('start_date', [$firstDay, $seventhDay])
                ->orWhereBetween('estimated_date', [$firstDay, $seventhDay]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assign_to');  // Asumiendo que 'assign_to' es el campo que guarda el ID del usuario
    }

    // public function milestone()
    // {
    //     return $this->milestone_id ? Milestone::find($this->milestone_id) : null;
    // }
    // Relación con Project (un Task pertenece a un Project)


    // public function users()
    // {
    //     return User::whereIn('id', explode(',', $this->assign_to))->get();
    // }
    // Relación muchos a muchos con los usuarios
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }
    public function taskUsers()
    {
        // User::select('users.*')->join('projects','projects.workspace', '=', 'users.currant_workspace');  
        return User::whereIn('id', explode(',', $this->assign_to))->get();
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    public function taskFiles()
    {
        return $this->hasMany('App\Models\TaskFile', 'task_id', 'id')->orderBy('id', 'DESC');
    }


    public function milestoneTitle()
    {
        $milestone = $this->milestone_id ? Milestone::find($this->milestone_id) : null;
        return $milestone ? $milestone->title : null;
    }

    public function sub_tasks()
    {
        return $this->hasMany('App\Models\SubTask', 'task_id', 'id')->orderBy('id', 'DESC');
    }
    public function daysLeft()
    {
        return  round((strtotime($this->due_date) - strtotime(date('Y-m-d'))) /   24 / 60 / 60,);
    }

    public function taskCompleteSubTaskCount()
    {
        return $this->sub_tasks->where('status', '=', '1')->count();
    }

    public function taskTotalSubTaskCount()
    {
        return $this->sub_tasks->count();
    }

    public function subTaskPercentage()
    {
        $completedChecklist = $this->taskCompleteSubTaskCount();
        $allChecklist = max($this->taskTotalSubTaskCount(), 1);

        $percentageNumber = ceil(($completedChecklist / $allChecklist) * 100);
        $percentageNumber = $percentageNumber > 100 ? 100 : ($percentageNumber < 0 ? 0 : $percentageNumber);

        return (int) number_format($percentageNumber);
    }
}
