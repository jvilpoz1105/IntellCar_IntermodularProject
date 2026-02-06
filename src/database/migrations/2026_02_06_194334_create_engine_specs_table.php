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
        Schema::create('engine_specs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('spec_name')->nullable(false);
            $table->string('value')->nullable(false);
            $table->string('meassurement_unit')->nullable(false);
            $table->enum('variable_type', ['numeric', 'text', 'boolean'])->default('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engine_specs');
    }
};
