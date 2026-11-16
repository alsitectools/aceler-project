<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El export a Axapta pasa a ser una línea por tarea y día trabajado:
 * el ledger guarda la tarea, la fecha de la línea y el RegId enviado (para no repetirlo).
 * Las filas antiguas quedan con task_id/work_date = null (formato anterior, por encargo).
 *
 * La tabla no la crea ninguna migración del repositorio y no es igual en todos los
 * entornos (en staging no existía task_id), así que cada cambio se aplica solo si falta.
 */
return new class extends Migration
{
    private const INDEX = 'ledger_milestone_employee_date_idx';

    public function up(): void
    {
        Schema::table('export_ledger_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('export_ledger_lines', 'task_id')) {
                $table->unsignedBigInteger('task_id')->nullable()->index()->after('milestone_id');
            }
        });

        Schema::table('export_ledger_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('export_ledger_lines', 'work_date')) {
                $table->date('work_date')->nullable()->after('task_id');
            }
            if (!Schema::hasColumn('export_ledger_lines', 'regid')) {
                $table->unsignedBigInteger('regid')->nullable()->unique()->after('work_date');
            }
        });

        if (!$this->hasIndex(self::INDEX)) {
            Schema::table('export_ledger_lines', function (Blueprint $table) {
                $table->index(['milestone_id', 'employee_number', 'work_date'], self::INDEX);
            });
        }
    }

    public function down(): void
    {
        Schema::table('export_ledger_lines', function (Blueprint $table) {
            if ($this->hasIndex(self::INDEX)) {
                $table->dropIndex(self::INDEX);
            }
            if (Schema::hasColumn('export_ledger_lines', 'regid')) {
                $table->dropUnique(['regid']);
            }
            $table->dropColumn(array_values(array_filter(
                ['work_date', 'regid'],
                fn ($column) => Schema::hasColumn('export_ledger_lines', $column)
            )));
        });
        // task_id no se elimina: en algunos entornos ya existía antes de esta migración
    }

    private function hasIndex(string $name): bool
    {
        return count(DB::select('SHOW INDEX FROM export_ledger_lines WHERE Key_name = ?', [$name])) > 0;
    }
};
