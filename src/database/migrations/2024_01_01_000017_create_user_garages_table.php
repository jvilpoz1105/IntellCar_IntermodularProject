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
        Schema::create('user_garage', function (Blueprint $table) {
            $table->id('garage_item_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('motor_id')->nullable();
            $table->string('car_nickname', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_current_car')->default(false);
            $table->string('photo_url', 790)->nullable();
            $table->boolean('verified_owner')->default(false);
            
            $table->foreign('user_id')->references('user_id')->on('app_user')->onDelete('cascade');
            $table->foreign('model_id')->references('model_id')->on('car_model')->onDelete('cascade');
            $table->foreign('motor_id')->references('engine_id')->on('car_engine')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_garage');
    }
};
