<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_settings', function (Blueprint $table) {
            $table->json('court_details')->nullable()->after('ball_rate');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('court_name')->nullable()->after('court_number');
        });
    }

    public function down(): void
    {
        Schema::table('facility_settings', function (Blueprint $table) {
            $table->dropColumn('court_details');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('court_name');
        });
    }
};
