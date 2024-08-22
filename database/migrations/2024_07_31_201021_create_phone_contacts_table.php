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
        Schema::create('phone_contacts', function (Blueprint $table) {
            $table->id();
            $table->integer('old_phone_contact_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name', 255);
            $table->string('last_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('work_email', 255)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('job_title', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 255);
            $table->string('work_phone', 255)->nullable();
            $table->string('website', 255)->nullable();
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
        Schema::dropIfExists('phone_contacts');
    }
};
