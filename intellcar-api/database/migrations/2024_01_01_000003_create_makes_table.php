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
        Schema::create('make', function (Blueprint $table) {
            $table->id('make_id');
            $table->string('make_name', 40)->unique();
            $table->string('origin_country', 45)->nullable();
            $table->string('official_website', 790)->nullable();
            $table->enum('status', ['low-cost', 'mass-market', 'premium', 'luxury'])->default('mass-market');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('make');
    }
};
