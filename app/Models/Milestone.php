<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'project_id', 'title', 'status', /*'tasks' ,*/ 'start_date', 'end_date', 'summary'
    ];
    public function daysLeft()
    {
        return  round((strtotime($this->end_date) - strtotime(date('Y-m-d'))) /   24 / 60 / 60,);
    }
    // public function tasks() 
    // {
    //     $milestone = Task::all();
        
    //     foreach ($tasks as $task) {
    //         if ($this->milestone_id =) {
    //         }
    //     }

    //     return $this->id ? Milestone::find($this->milestone_id) : null;
    // }
}
