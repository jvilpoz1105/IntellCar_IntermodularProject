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
        Schema::create('car_model', function (Blueprint $table) {
            $table->id('model_id');
            $table->string('model_name', 101);
            $table->unsignedBigInteger('make_id');
            $table->string('model_description', 255)->nullable();
            
            $table->foreign('make_id')->references('make_id')->on('make')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_model');
    }
};
