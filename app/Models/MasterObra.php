<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterObra extends Model
{
    protected $fillable = [
        'ref_mo', 'name','business_unit','status','enterprise_id','project_id'
    ];
}
