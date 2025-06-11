<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckDuplicateRoutes extends Command
{
    protected $signature = 'routes:duplicates';
    protected $description = 'Find duplicate route names in the project';

    public function handle()
    {
        $routeNames = [];
        $duplicates = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (!$name) {
                continue;
            }

            if (isset($routeNames[$name])) {
                $duplicates[$name][] = $route->uri();
                $duplicates[$name][] = $routeNames[$name];
            } else {
                $routeNames[$name] = $route->uri();
            }
        }

        if (empty($duplicates)) {
            $this->info('✅ No duplicate route names found!');
        } else {
            $this->error('⚠️ Duplicate route names found:');
            foreach ($duplicates as $name => $uris) {
                $this->line("Route name [{$name}] is used in:");
                foreach (array_unique($uris) as $uri) {
                    $this->line("  - /{$uri}");
                }
                $this->line('');
            }
        }

        return 0;
    }
}
