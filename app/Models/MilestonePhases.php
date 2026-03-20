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

    public const PHASE_TRANSLATION_KEYS = [
        'Planificación' => 'Planning',
        'Diseño' => 'Design',
        'Implementación' => 'Implementation',
        'Documentación' => 'Documentation',
        'Validación funcional' => 'Functional validation',
        'Explotación comercial' => 'Commercial exploitation',
    ];

    public static function translationKey(?string $phase): string
    {
        return self::PHASE_TRANSLATION_KEYS[$phase] ?? ($phase ?? '');
    }
}
