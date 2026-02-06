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
        Schema::create('event_kdd', function (Blueprint $table) {
            $table->id('event_id');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('paddock_id')->nullable();
            $table->string('title', 150);
            $table->text('event_description');
            $table->dateTime('event_date');
            $table->string('location_name', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('max_participants')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            
            $table->foreign('creator_id')->references('user_id')->on('app_user')->onDelete('cascade');
            $table->foreign('paddock_id')->references('paddock_id')->on('paddock')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_kdd');
    }
};
