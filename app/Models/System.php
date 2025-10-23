<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\select;

class System extends Model
{

    protected $table = 'system'; // Nombre de la tabla en la BD
    protected $primaryKey = 'id_system'; // Clave primaria (Laravel la detecta automáticamente)
    public $timestamps = false;

    protected $fillable = [
        'code_system', 'color', 'name_system', 'group_system', 'img'
    ];
}
