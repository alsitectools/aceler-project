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
        // 'description',
        'start_date',
        'estimated_date',
    ];
    // use for invoice details
    public function invoiceproject()
    {
        return $this->belongsTo('App\Models\Project', 'project_id', 'id');
    }

    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

    public function users()
    {
        return User::whereIn('id', explode(',', $this->assign_to))->get();
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

    public function milestone()
    {
        return $this->milestone_id ? Milestone::find($this->milestone_id) : null;
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
