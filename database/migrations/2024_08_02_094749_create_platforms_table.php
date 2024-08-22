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
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->integer('old_platform_id');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('icon', 255)->nullable();
            $table->string('input', 255)->default('username');
            $table->string('baseURL', 255)->nullable();
            $table->boolean('pro')->default(0);
            $table->boolean('status')->default(1);
            $table->string('placeholder_en', 255)->nullable();
            $table->string('placeholder_sv', 255)->nullable();
            $table->string('description_en', 255)->nullable();
            $table->string('description_sv', 255)->nullable();
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
        Schema::dropIfExists('platforms');
    }
};
