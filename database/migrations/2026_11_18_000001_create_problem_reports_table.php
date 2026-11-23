<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * «Reportar un problema» (enlace del pie del menú): problemas y sugerencias que envían los usuarios.
 * La página, el navegador y los errores de la página se guardan solos para no tener que preguntar
 * «¿dónde te pasó?». Lo gestionan los usuarios con users.is_support.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('problem_reports')) {
            return;
        }

        Schema::create('problem_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('workspace_id')->nullable();
            $table->string('type', 20)->default('problem');      // problem | suggestion
            $table->text('description');
            $table->text('expected')->nullable();                // «¿Qué esperabas que pasara?» (solo problemas)
            $table->string('page_url', 500)->nullable();
            $table->string('page_title', 255)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('viewport', 30)->nullable();
            $table->text('js_errors')->nullable();               // últimos errores de la página, para depurar
            $table->string('screenshot_path', 255)->nullable();  // storage/app (privado)
            $table->string('status', 20)->default('received')->index(); // received | in_review | done | discarded
            $table->text('admin_note')->nullable();              // respuesta de soporte, la ve quien lo envió
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_reports');
    }
};
