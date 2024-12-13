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
    public function tasks()
    {
        return Task::join('task_types', 'task_types.id', '=', 'tasks.type_id')
            ->where('tasks.milestone_id', $this->id)
            ->select('tasks.*', 'task_types.name as task_name')
            ->get();
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
