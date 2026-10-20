<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Divide el contador de obra por delegación en series separadas O/X:
     * PK (delegation_id, letter). Las filas existentes pasan a la serie 'O'.
     * NOTA: en entornos ya actualizados a mano (columna letter existente) no hace nada.
     * La renumeración de milestones sin M.O. existentes se aplica aparte con SQL de datos.
     */
    public function up(): void
    {
        if (!Schema::hasTable('obra_counters') || Schema::hasColumn('obra_counters', 'letter')) {
            return;
        }

        Schema::table('obra_counters', function (Blueprint $table) {
            $table->dropPrimary();
            $table->char('letter', 1)->notNull()->after('delegation_id');
        });

        DB::table('obra_counters')->update(['letter' => 'O']);

        Schema::table('obra_counters', function (Blueprint $table) {
            $table->primary(['delegation_id', 'letter']);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('obra_counters') || !Schema::hasColumn('obra_counters', 'letter')) {
            return;
        }

        DB::table('obra_counters')->where('letter', 'X')->delete();

        Schema::table('obra_counters', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('letter');
        });

        Schema::table('obra_counters', function (Blueprint $table) {
            $table->primary('delegation_id');
        });
    }
};
