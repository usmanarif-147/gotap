<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('username', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('is_suspended')->default(0);
            $table->string('password', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->tinyInteger('gender')->nullable()->default(1)->comment('1=male,2=female,3=non-binary,4=not share');
            $table->string('dob', 255)->nullable();
            $table->tinyInteger('verified')->default(0);
            $table->tinyInteger('featured')->default(0);
            $table->string('fcm_token', 255)->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->tinyInteger('is_email_sent')->default(0);
            $table->integer('tiks')->default(0);
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
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
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('sessions');
    }
};
