<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\select;

class PuntuacionTarea extends Model
{
    protected $table = 'puntuacion_tarea'; // Nombre de la tabla en la BD
    protected $primaryKey = 'Id'; // Clave primaria (Laravel la detecta automáticamente)
    public $timestamps = false;

    protected $fillable = [
        'id_tarea', 'cantidad_puntaje', 'user_id','puntos_hora'
    ];
}
