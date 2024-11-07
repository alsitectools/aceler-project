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
        Schema::create('potential_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('business_unit');
            $table->string('potential_customer_id');
            $table->string('customer_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potential_clients');
    }
};
