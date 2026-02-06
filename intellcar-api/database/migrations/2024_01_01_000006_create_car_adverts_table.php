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
        Schema::create('car_advert', function (Blueprint $table) {
            $table->id('ad_id');
            $table->string('ad_title', 165);
            $table->enum('ad_type', ['new', 'km0', 'used', 'renting', 'leasing', 'supcription']);
            $table->text('ad_details')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('kilometers')->default(0);
            $table->enum('car_color', ['blanco', 'negro', 'gris', 'plata', 'rojo', 'azul', 'verde', 'amarillo', 'naranja', 'otro']);
            $table->integer('year_manufacture')->nullable();
            $table->string('region', 100);
            $table->string('city', 100);
            $table->boolean('visible')->default(false);
            $table->timestamp('publish_date')->useCurrent();
            
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('engine_id');
            $table->unsignedBigInteger('seller_id');
            
            $table->foreign('model_id')->references('model_id')->on('car_model')->onDelete('cascade');
            $table->foreign('engine_id')->references('engine_id')->on('car_engine')->onDelete('cascade');
            $table->foreign('seller_id')->references('user_id')->on('app_user')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_advert');
    }
};
