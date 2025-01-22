<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTimetable extends Model
{
    use HasFactory;

    protected $table = 'user_timetable';

    protected $fillable = [
        'user_id',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'special_days_range',
        'intensive_workday',
    ];
}
