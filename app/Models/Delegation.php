<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delegation extends Model
{
    use HasFactory;

    protected $table = 'delegations';
    protected $primaryKey = 'id';
    public $incrementing = false; // ❌ importante
    protected $keyType = 'string'; // ❌ clave primaria tipo string
    protected $fillable = ['delegation_name'];

    public function workspace()
    {
        return $this->hasOne('App\Models\Workspace', 'delegation_id', 'id');
    }
}
