<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomTasks extends Model
{
    protected $table = 'custom_tasks'; 

    protected $fillable = [
        'id_task',
        'name'
    ];
   
    public $timestamps = false; 

     public function task()
    {
        return $this->belongsTo(Task::class, 'id_task');
    }

}
