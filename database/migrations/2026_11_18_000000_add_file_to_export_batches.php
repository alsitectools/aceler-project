<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copia del .fil de cada lote del export a Axapta, para poder redescargarlo desde el perfil.
 * - file_content: el .fil comprimido con gzip (se descomprime al descargar: sale idéntico).
 *   MEDIUMBLOB (hasta 16 MB): un lote de 20.000 líneas ocupa ~0,5 MB comprimido.
 * - file_hash: SHA-256 del .fil sin comprimir, para comprobar que se descarga el original.
 * Solo añade columnas; los lotes anteriores quedan sin archivo.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('export_batches', 'file_content')) {
            // Blueprint::binary() crea un BLOB de 64 KB: se pide MEDIUMBLOB explícitamente
            DB::statement('ALTER TABLE export_batches ADD file_content MEDIUMBLOB NULL');
        }

        Schema::table('export_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('export_batches', 'file_hash')) {
                $table->char('file_hash', 64)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('export_batches', function (Blueprint $table) {
            $table->dropColumn(['file_content', 'file_hash']);
        });
    }
};
