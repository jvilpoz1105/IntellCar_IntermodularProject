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
        Schema::create('saved_search', function (Blueprint $table) {
            $table->id('search_id');
            $table->unsignedBigInteger('user_id');
            $table->string('search_name', 100);
            $table->json('filters_json');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('user_id')->references('user_id')->on('app_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_search');
    }
};
