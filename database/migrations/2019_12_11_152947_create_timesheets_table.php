<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'timesheets',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('project_id');
                $table->integer('task_id');
                $table->date('date')->nullable();
                $table->time('time')->default('00:00');
                $table->integer('created_by')->default('0');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheets');
    }
}
