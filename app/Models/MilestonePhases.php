<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MilestonePhases extends Model
{
    protected $table = 'milestone_phases'; 

    protected $fillable = [
        'id_milestone',
        'phases'
    ];
   
    public $timestamps = false; 

     public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'id_milestone');
    }

    // Opcional: lista de valores permitidos (por si quieres validar)
    public const PHASES = [
        'Planificación',
        'Diseño',
        'Implementación',
        'Documentación',
        'Validación funcional',
        'Explotación comercial'
    ];
}
