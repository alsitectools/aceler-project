<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotentialClient extends Model
{
    use HasFactory;
    protected $fillable = [
       'name','potencial_.consumer_id','customer_id'
    ];
}
