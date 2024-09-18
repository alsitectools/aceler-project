<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('userPrincipalName');
            $table->string('company');
            $table->string('branch');
            $table->string('department');
            $table->string('country');
            $table->string('jobTitle');
            $table->string('officeLocation');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('type');
            $table->integer('currant_workspace');
            $table->string('lang', 5)->default(Config::get('app.locale'));
            $table->rememberToken();
            $table->string('avatar')->nullable();
            $table->timestamps();
            $table->timestamp('email_verified_at');
            $table->string('messenger_color')->default('#2180f3');
            $table->boolean('dark_mode')->default(0);
            $table->boolean('active_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
