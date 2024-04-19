<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterObra extends Model
{
    protected $fillable = [
        'ref_mo', 'name'
    ];
}
