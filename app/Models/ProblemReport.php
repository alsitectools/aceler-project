<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Problema o sugerencia enviado desde «Reportar un problema» (pie del menú).
 */
class ProblemReport extends Model
{
    protected $table = 'problem_reports';

    public const TYPES = ['problem', 'suggestion'];
    public const STATUSES = ['received', 'in_review', 'done', 'discarded'];

    protected $fillable = [
        'user_id', 'workspace_id', 'type', 'description', 'expected',
        'page_url', 'page_title', 'user_agent', 'viewport', 'js_errors', 'screenshot_path',
        'status', 'admin_note', 'handled_by', 'status_changed_at',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /** Textos de cada estado (traducidos) */
    public static function statusLabels(): array
    {
        return [
            'received' => __('Received'),
            'in_review' => __('Under review'),
            'done' => __('Done'),
            'discarded' => __('Discarded'),
        ];
    }

    public static function typeLabels(): array
    {
        return [
            'problem' => __('Something is not working'),
            'suggestion' => __('Suggestion'),
        ];
    }

    /** Informes nuevos (sin mirar): la banderita de la foto de perfil de soporte */
    public static function newCount(): int
    {
        return self::where('status', 'received')->count();
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }
}
