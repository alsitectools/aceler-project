<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMilestoneFilesTable extends Migration
{
    public function up()
    {
        Schema::create('milestone_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('milestone_id'); // Relación con el milestone
            $table->string('file'); // Nombre del archivo guardado
            $table->string('name'); // Nombre original del archivo
            $table->string('extension'); // Extensión del archivo
            $table->string('file_size'); // Tamaño del archivo
            $table->unsignedBigInteger('created_by'); // Usuario que subió el archivo
            $table->string('user_type'); // Tipo de usuario (User o Client)
            $table->timestamps();

            // Relación con la tabla milestones
            $table->foreign('milestone_id')->references('id')->on('milestones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('milestone_files');
    }
}
