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
        Schema::create('car_engine', function (Blueprint $table) {
            $table->id('engine_id');
            $table->string('engine_name', 101);
            $table->string('engine_description', 255)->nullable();
            $table->enum('fuel_type', ['gasolina', 'diesel', 'electrico', 'hibrido', 'glp']);
            $table->unsignedBigInteger('make_id');
            
            $table->foreign('make_id')->references('make_id')->on('make')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_engine');
    }
};
