<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneStageProject extends Model
{
    protected $table = 'milestone_stages_project';

    protected $fillable = [
        'project_id',
        'name',
    ];

    public $timestamps = false;
}
