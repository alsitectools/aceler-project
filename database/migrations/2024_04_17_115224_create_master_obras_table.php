<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_obras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ref_mo')->nullable(); // work_id
            $table->string('name');
            $table->string('business_unit');
            $table->string('status')->default(null);
            $table->string('potential_customer_id')->default(0); //potencial_customer_id
            $table->integer('project_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_obras');
    }
};
