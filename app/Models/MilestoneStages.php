<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneStages extends Model
{
    protected $table = 'milestone_stages'; 

    protected $fillable = [
        'id_milestone',
        'stages',
        'milestone_stage_project_id'
    ];
   
    public $timestamps = false; 

     public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'id_milestone');
    }

    public function stageProject()
    {
        return $this->belongsTo(MilestoneStageProject::class, 'milestone_stage_project_id');
    }

    //lista de valores permitidos
    public const STAGES = [
        'Planificación',
        'Validación',
        'Explotación comercial',
        'Documentación'
    ];
}
