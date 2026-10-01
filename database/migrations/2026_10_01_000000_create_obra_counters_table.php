<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObraCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('obra_counters')) {
            Schema::create('obra_counters', function (Blueprint $table) {
                $table->string('delegation_id')->primary();
                $table->integer('current_number')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('milestones', 'obra_number')) {
            Schema::table('milestones', function (Blueprint $table) {
                $table->integer('obra_number')->nullable()->after('project_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('milestones', 'obra_number')) {
            Schema::table('milestones', function (Blueprint $table) {
                $table->dropColumn('obra_number');
            });
        }

        Schema::dropIfExists('obra_counters');
    }
}