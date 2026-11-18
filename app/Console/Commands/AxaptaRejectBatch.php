<?php

namespace App\Console\Commands;

use App\Models\ExportBatch;
use App\Models\ExportLedgerLine;
use Illuminate\Console\Command;

/**
 * Marca lotes del export a Axapta como rechazados por Axapta.
 * Sus líneas dejan de contar como enviadas y las horas vuelven a salir en el
 * siguiente export. No borra nada; con --undo se quita la marca.
 */
class AxaptaRejectBatch extends Command
{
    protected $signature = 'axapta:reject-batch
                            {ids?* : Ids de los lotes (export_batches.id)}
                            {--undo : Quitar la marca de rechazado}
                            {--list : Solo listar los lotes}
                            {--force : No pedir confirmación}';

    protected $description = 'Marca lotes del export a Axapta como rechazados (no borra nada)';

    public function handle(): int
    {
        $this->table(['Id', 'Fichero', 'Fecha', 'Líneas', 'Horas', 'Rechazado'],
            ExportBatch::select(ExportBatch::LIST_COLUMNS)->orderBy('id')->get()->map(fn($b) => [
                $b->id,
                $b->file_name,
                $b->created_at,
                ExportLedgerLine::where('batch_id', $b->id)->count(),
                round((float) ExportLedgerLine::where('batch_id', $b->id)->sum('hours_decimal'), 2),
                $b->rejected_at ?? '-',
            ])
        );

        $ids = $this->argument('ids');
        if ($this->option('list') || empty($ids)) {
            return self::SUCCESS;
        }

        $batches = ExportBatch::whereIn('id', $ids)->get();
        if ($batches->count() !== count($ids)) {
            $this->error('No existen los lotes: ' . implode(', ', array_diff($ids, $batches->pluck('id')->all())));

            return self::FAILURE;
        }

        $undo = (bool) $this->option('undo');
        $action = $undo ? 'quitar la marca de rechazado a' : 'marcar como rechazados';
        if (!$this->option('force') && !$this->confirm("¿Seguro que quieres $action los lotes " . implode(', ', $ids) . '?')) {
            $this->info('Cancelado.');

            return self::SUCCESS;
        }

        ExportBatch::whereIn('id', $ids)->update(['rejected_at' => $undo ? null : now()]);
        $this->info('Hecho. ' . ($undo
            ? 'Esas líneas vuelven a contar como enviadas.'
            : 'Esas horas saldrán de nuevo en el próximo export.'));

        return self::SUCCESS;
    }
}
