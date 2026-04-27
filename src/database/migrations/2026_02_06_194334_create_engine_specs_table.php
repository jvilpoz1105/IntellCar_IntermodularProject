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
        Schema::create('engine_spec', function (Blueprint $table) {
            $table->id('spec_id');
            $table->string('sp_key');
            $table->string('sp_value');
            $table->string('measurement_unit')->nullable();
            $table->enum('variable_type', ['numeric', 'text', 'boolean'])->default('text');
            $table->unsignedBigInteger('sp_engine');

            $table->foreign('sp_engine')->references('engine_id')->on('car_engine')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engine_spec');
    }
};
