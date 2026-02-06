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
        Schema::create('app_user', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_name', 90);
            $table->string('email_address', 255)->unique();
            $table->string('contact_email', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 20)->unique();
            $table->string('user_password', 255);
            $table->enum('user_tag', ['admin', 'pro', 'indv', 'press'])->default('indv');
            $table->timestamp('registration_date')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('paddock_id')->nullable();
            
            $table->foreign('paddock_id')->references('paddock_id')->on('paddock')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_user');
    }
};
