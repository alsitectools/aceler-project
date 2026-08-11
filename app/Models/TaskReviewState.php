<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReviewState extends Model
{
    protected $fillable = [
        'task_id',
        'milestone_id',
        'state_code',
        'mark_user_id',
        'task_owner_user_id',
        'milestone_created_by',
        'comment',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}