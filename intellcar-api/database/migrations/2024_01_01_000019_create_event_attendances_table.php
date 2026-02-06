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
        Schema::create('event_attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('joined_at')->useCurrent();
            
            $table->primary(['event_id', 'user_id']);
            $table->foreign('event_id')->references('event_id')->on('event_kdd')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('app_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendance');
    }
};
