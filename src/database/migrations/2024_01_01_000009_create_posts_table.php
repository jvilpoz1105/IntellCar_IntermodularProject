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
        Schema::create('post', function (Blueprint $table) {
            $table->id('post_id');
            $table->unsignedBigInteger('author_id');
            $table->string('title', 150)->nullable();
            $table->text('content');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('engine_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            
            $table->foreign('model_id')->references('model_id')->on('car_model')->onDelete('cascade');
            $table->foreign('engine_id')->references('engine_id')->on('car_engine')->onDelete('cascade');
            $table->foreign('author_id')->references('user_id')->on('app_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post');
    }
};
