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
        Schema::create('ad_media', function (Blueprint $table) {
            $table->id('media_id');
            $table->string('media_url', 790);
            $table->enum('media_type', ['image', 'video']);
            $table->unsignedBigInteger('ad_id');
            
            $table->foreign('ad_id')->references('ad_id')->on('car_advert')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_media');
    }
};
