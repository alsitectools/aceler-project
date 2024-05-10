<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('project_id');
            $table->string('title');
            /* Hay que hacer la migracion, determinar nombre de Task ¿En plural o no?*/
            $table->integer('assign_to');
            $table->string('tasks')->nullable();
            $table->string('status')->default('todo');
            $table->string('order')->default(0);
            $table->date('end_date');
            $table->date('start_date')->nullable();
            $table->text('summary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('milestones');
    }
}
