<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\select;

class Puntuacion extends Model
{
    protected $table = 'puntuacion'; // Nombre de la tabla en la BD
    protected $primaryKey = 'Id'; // Clave primaria (Laravel la detecta automáticamente)
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'valor'
    ];
}
