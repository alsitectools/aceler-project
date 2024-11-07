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
            $table->integer('assign_to');
            $table->string('status')->default(1);
            $table->string('order')->default(1);
            $table->string('company')->nullable();
            $table->string('contractor')->nullable();
            $table->string('contractorAdress')->nullable();
            $table->string('jobsiteAdress')->nullable();
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
