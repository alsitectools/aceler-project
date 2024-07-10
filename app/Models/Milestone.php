<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'assign_to',
        'tasks',
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
        return Timesheet::join('tasks', 'timesheets.task_id', '=', 'tasks.id')
            ->where('tasks.milestone_id', $this->id)
            ->get();
    }


    public function milestone()
    {
        $milestone = Milestone::join('tasks', 'milestones.id', '=', 'tasks.milestone_id')
            ->where('milestone_id', $this->id)->first();

        return $milestone ? $milestone->title : null;
    }
}
