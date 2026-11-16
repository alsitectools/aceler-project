<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El export a Axapta pasa a ser una línea por tarea y día trabajado:
 * el ledger guarda la fecha de la línea y el RegId enviado (para no repetirlo).
 * Las filas antiguas quedan con work_date = null (formato anterior, por encargo).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_ledger_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('export_ledger_lines', 'work_date')) {
                $table->date('work_date')->nullable()->after('task_id');
            }
            if (!Schema::hasColumn('export_ledger_lines', 'regid')) {
                $table->unsignedBigInteger('regid')->nullable()->unique()->after('work_date');
            }
            $table->index(['milestone_id', 'employee_number', 'work_date'], 'ledger_milestone_employee_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('export_ledger_lines', function (Blueprint $table) {
            $table->dropIndex('ledger_milestone_employee_date_idx');
            $table->dropUnique(['regid']);
            $table->dropColumn(['work_date', 'regid']);
        });
    }
};
