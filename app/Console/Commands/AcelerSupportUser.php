<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Da o quita el permiso «Recibe reportes» (users.is_support): quien lo tiene recibe los
 * problemas y sugerencias de «Reportar un problema» y gestiona la bandeja de reportes.
 */
class AcelerSupportUser extends Command
{
    protected $signature = 'aceler:support
                            {email? : Email del usuario}
                            {--remove : Quitar el permiso}';

    protected $description = 'Da o quita el permiso «Recibe reportes» (users.is_support); sin email, lista quién lo tiene';

    public function handle(): int
    {
        $email = $this->argument('email');

        if (!$email) {
            $users = User::where('is_support', 1)->orderBy('name')->get(['id', 'name', 'email']);
            $users->isEmpty()
                ? $this->info('Nadie tiene el permiso «Recibe reportes».')
                : $this->table(['Id', 'Nombre', 'Email'], $users->map(fn($u) => [$u->id, $u->name, $u->email]));

            return self::SUCCESS;
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("No existe ningún usuario con el email $email.");

            return self::FAILURE;
        }

        $user->is_support = $this->option('remove') ? 0 : 1;
        $user->save();
        $this->info(($this->option('remove') ? 'Quitado' : 'Dado') . " el permiso «Recibe reportes» a {$user->name}.");

        return self::SUCCESS;
    }
}
