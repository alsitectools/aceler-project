<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTimetable extends Model
{
    use HasFactory;

    protected $table = 'user_timetable';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'range_holidays',
        'range_intensive_workday',
    ];
}
