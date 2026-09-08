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
        Schema::table('clients_mos', function (Blueprint $table) {
            $table->index('ref_mo');
        });

        Schema::table('potential_clients', function (Blueprint $table) {
            $table->index('potential_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_mos', function (Blueprint $table) {
            $table->dropIndex(['ref_mo']);
        });

        Schema::table('potential_clients', function (Blueprint $table) {
            $table->dropIndex(['potential_customer_id']);
        });
    }
};
