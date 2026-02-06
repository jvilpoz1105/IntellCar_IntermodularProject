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
        Schema::create('advert_moods', function (Blueprint $table) {
            $table->unsignedBigInteger('ad_id');
            $table->unsignedBigInteger('mood_id');
            
            $table->primary(['ad_id', 'mood_id']);
            $table->foreign('ad_id')->references('ad_id')->on('car_advert')->onDelete('cascade');
            $table->foreign('mood_id')->references('paddock_id')->on('paddock')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advert_moods');
    }
};
