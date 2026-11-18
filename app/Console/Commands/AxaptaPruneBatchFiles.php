<?php

namespace App\Console\Commands;

use App\Models\ExportBatch;
use Illuminate\Console\Command;

/**
 * Borra la copia del .fil de los lotes del export a Axapta más antiguos que el límite
 * (config axapta.batch_file_retention_months). Solo vacía el archivo: el lote, sus líneas
 * y su huella se conservan, así que el cálculo de lo exportado no cambia.
 * Programado a diario en App\Console\Kernel; con --dry-run solo lista lo que borraría.
 */
class AxaptaPruneBatchFiles extends Command
{
    protected $signature = 'axapta:prune-batch-files
                            {--months= : Meses a conservar (por defecto, config axapta.batch_file_retention_months)}
                            {--dry-run : Solo listar, sin borrar}';

    protected $description = 'Borra la copia del archivo de los lotes de Axapta antiguos (conserva el lote y sus líneas)';

    public function handle(): int
    {
        $months = $this->option('months') !== null
            ? (int) $this->option('months')
            : (int) config('axapta.batch_file_retention_months', 6);

        if ($months <= 0) {
            $this->info('Límite desactivado (0 meses): no se borra nada.');

            return self::SUCCESS;
        }

        $cutoff = now()->subMonths($months);
        $query = ExportBatch::whereNotNull('file_content')->where('created_at', '<', $cutoff);
        $batches = (clone $query)->select(ExportBatch::LIST_COLUMNS)->orderBy('id')->get();

        if ($batches->isEmpty()) {
            $this->info("No hay archivos de lotes anteriores al {$cutoff->format('d/m/Y')}.");

            return self::SUCCESS;
        }

        $this->table(['Id', 'Fichero', 'Fecha'], $batches->map(fn($b) => [$b->id, $b->file_name, $b->created_at]));

        if ($this->option('dry-run')) {
            $this->info("Se borraría el archivo de {$batches->count()} lotes (simulación, no se ha tocado nada).");

            return self::SUCCESS;
        }

        $updated = $query->update(['file_content' => null]);
        $this->info("Borrado el archivo de $updated lotes anteriores al {$cutoff->format('d/m/Y')}. Los lotes y sus líneas se conservan.");
        \Log::info("Export Axapta: borrado el archivo de $updated lotes anteriores al {$cutoff->toDateString()}");

        return self::SUCCESS;
    }
}
