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
        Schema::create('post_moods', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('mood_id');
            
            $table->primary(['post_id', 'mood_id']);
            $table->foreign('post_id')->references('post_id')->on('post')->onDelete('cascade');
            $table->foreign('mood_id')->references('paddock_id')->on('paddock')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_moods');
    }
};
