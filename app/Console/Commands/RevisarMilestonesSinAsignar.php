<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Milestone;
use App\Mail\MilestoneSinAsignarMail;
use Carbon\Carbon;

class RevisarMilestonesSinAsignar extends Command
{
    protected $signature = 'milestones:revisar';
    protected $description = 'Envía recordatorios de milestones sin asignar a los participantes del proyecto';

    public function handle()
    {
        Log::info('[milestones:revisar] Inicio del comando');

        $milestones = Milestone::where(function ($query) {
            $query->whereNull('milestone_assigned_to_user')
                ->orWhere('milestone_assigned_to_user', '');
        })
            ->where('reminder_mail_is_send', false)
            // ->whereDate('created_at', Carbon::yesterday()) // creadas ayer
            ->with('project.workspaceData') // cargamos relación proyecto + workspace
            ->get();

        Log::info("[milestones:revisar] Milestones sin asignar encontrados: {$milestones->count()}");

        foreach ($milestones as $milestone) {
            if (!$milestone->project) {
                Log::warning("[milestones:revisar] Milestone ID {$milestone->id} no tiene proyecto asociado, se omite");
                continue;
            }

            $projectId = $milestone->project_id;
            $projectName = $milestone->project->name;

            Log::info("[milestones:revisar] Procesando milestone ID {$milestone->id} ('{$milestone->title}') del proyecto '{$projectName}' (ID {$projectId})");

            // buscamos usuarios que participan en el proyecto (user_projects)
            $usuarios = \App\Models\User::whereIn('id', function ($q) use ($projectId) {
                $q->select('user_id')
                    ->from('user_projects')
                    ->where('project_id', $projectId)
                    ->where('is_active', 1);
            })->get();

            Log::info("[milestones:revisar] Usuarios encontrados en proyecto {$projectId}: {$usuarios->count()}");

            foreach ($usuarios as $user) {
                Log::info("[milestones:revisar]   -> Enviando correo a {$user->email} (ID {$user->id})");
                Mail::to($user->email)->send(new MilestoneSinAsignarMail($milestone, $user));
            }

            $milestone->update(['reminder_mail_is_send' => true]);

            $this->info("Correos enviados para milestone ID {$milestone->id} en proyecto {$projectId}");
        }

        if ($milestones->isEmpty()) {
            $this->info("No se encontraron milestones sin asignar.");
        }

        Log::info('[milestones:revisar] Fin del comando');
    }
}
