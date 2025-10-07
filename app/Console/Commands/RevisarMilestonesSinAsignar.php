<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Milestone;
use App\Mail\MilestoneSinAsignarMail;
use Carbon\Carbon;

class RevisarMilestonesSinAsignar extends Command
{
    protected $signature = 'milestones:revisar';
    protected $description = 'Envía recordatorios de milestones sin asignar a los usuarios del workspace correspondiente';

    public function handle()
    {
        $milestones = Milestone::whereNull('milestone_assigned_to_user')
            ->where('reminder_mail_is_send', false)
            // ->whereDate('created_at', Carbon::yesterday()) // creadas ayer
            ->with('project.workspaceData') // cargamos relación
            ->get();

        foreach ($milestones as $milestone) {
            if (!$milestone->project) {
                continue;
            }

            $workspaceId = $milestone->project->workspace;

            // buscamos usuarios de ese workspace
            $usuarios = \App\Models\User::where('currant_workspace', $workspaceId)->get();

            foreach ($usuarios as $user) {
                //  no ponemos ->from(), que use lo de .env
                Mail::to($user->email)->send(new MilestoneSinAsignarMail($milestone, $user));
            }

            $milestone->update(['reminder_mail_is_send' => true]);

            $this->info("Correos enviados para milestone ID {$milestone->id} en workspace {$workspaceId}");
        }

        if ($milestones->isEmpty()) {
            $this->info("No se encontraron milestones sin asignar.");
        }
    }
}
