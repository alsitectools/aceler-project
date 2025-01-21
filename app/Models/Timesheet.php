<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'project_id',
        'task_id',
        'date',
        'time',
        // 'description',
        'created_by',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }


    // Relación con Project
    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'project_id');
    }

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}
