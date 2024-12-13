<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilestoneFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_id',
        'file',
        'name',
        'extension',
        'file_size',
        'created_by',
        'user_type',
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }
}
