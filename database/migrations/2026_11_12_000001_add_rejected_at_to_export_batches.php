<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un lote que Axapta rechazó se marca con rejected_at: sus líneas dejan de
 * contar como exportadas y las horas vuelven a salir en el siguiente export.
 * No se borra nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('export_batches', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('export_batches', function (Blueprint $table) {
            $table->dropColumn('rejected_at');
        });
    }
};
