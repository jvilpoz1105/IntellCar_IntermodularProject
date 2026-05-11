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
            $table->json('ai_metadata')->nullable()->after('ad_details');
        });

        Schema::table('post', function (Blueprint $table) {
            $table->json('ai_metadata')->nullable()->after('content');
        });

        Schema::table('event_kdd', function (Blueprint $table) {
            $table->json('ai_metadata')->nullable()->after('event_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_advert', function (Blueprint $table) {
            $table->dropColumn('ai_metadata');
        });

        Schema::table('post', function (Blueprint $table) {
            $table->dropColumn('ai_metadata');
        });

        Schema::table('event_kdd', function (Blueprint $table) {
            $table->dropColumn('ai_metadata');
        });
    }
};
