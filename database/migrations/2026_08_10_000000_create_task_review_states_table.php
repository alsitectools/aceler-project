<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('task_review_states', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('task_id')->index();
            $table->bigInteger('milestone_id')->nullable();
            $table->string('state_code', 50);
            $table->bigInteger('mark_user_id')->nullable();
            $table->bigInteger('task_owner_user_id')->nullable();
            $table->bigInteger('milestone_created_by')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('task_review_states');
    }
};