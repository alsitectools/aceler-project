<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'assign_to',
        'status',
        'order',
        'start_date',
        'end_date',
        'summary'
    ];

    public function daysLeft()
    {
        return  round((strtotime($this->end_date) - strtotime(date('Y-m-d'))) /   24 / 60 / 60,);
    }

    function tasks()
    {
        return Task::join('task_types', 'task_types.id', 'tasks.type_id')
            ->where('tasks.milestone_id', $this->id)->pluck('task_types.name');
    }

    public function salesManager()
    {

        $sales_manager = User::join('milestones', 'milestones.assign_to', '=', 'users.id')
            ->where('milestones.assign_to', $this->assign_to)
            ->select('users.*')
            ->first();



        return $sales_manager ? $sales_manager->name : "Hola";
    }

    public function milestone()
    {
        $milestone = Milestone::join('tasks', 'milestones.id', '=', 'tasks.milestone_id')
            ->where('milestone_id', $this->id)->first();

        return $milestone ? $milestone->title : null;
    }
}
