<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permiso «Recibe reportes»: quien lo tiene recibe los problemas y sugerencias que envían los
 * usuarios (aviso en la campana) y gestiona la bandeja de reportes. Igual que is_exporter.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_support')) {
                $table->boolean('is_support')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_support');
        });
    }
};
