<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('slug');
            $table->integer('created_by');
            $table->string('lang', 5)->default('es');
            $table->string('currency');
            // $table->string('company')->nullable();
            // $table->string('branch');
            $table->integer('interval_time')->default(10);
            // $table->string('currency_code')->nullable();
            // $table->string('address')->nullable();
            // $table->string('city')->nullable();
            // $table->string('state')->nullable();
            // $table->string('zipcode')->nullable();
            $table->string('country')->nullable();
            // $table->string('telephone')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo_white')->nullable();
            $table->string('site_rtl')->nullable();
            $table->integer('is_stripe_enabled')->default(0);
            $table->integer('is_paypal_enabled')->default(0);
            $table->string('is_googlecalendar_enabled');
            $table->string('google_calender_id');
            $table->string('google_calender_json_file');
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('workspaces');
    }
}
