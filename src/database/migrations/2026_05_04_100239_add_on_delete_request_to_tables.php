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
        Schema::table('car_advert', function (Blueprint $table) {
            $table->timestamp('onDeleteRequest')->nullable();
        });

        Schema::table('post', function (Blueprint $table) {
            $table->timestamp('onDeleteRequest')->nullable();
        });

        Schema::table('event_kdd', function (Blueprint $table) {
            $table->timestamp('onDeleteRequest')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_advert', function (Blueprint $table) {
            $table->dropColumn('onDeleteRequest');
        });

        Schema::table('post', function (Blueprint $table) {
            $table->dropColumn('onDeleteRequest');
        });

        Schema::table('event_kdd', function (Blueprint $table) {
            $table->dropColumn('onDeleteRequest');
        });
    }
};
