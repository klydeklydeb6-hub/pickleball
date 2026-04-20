<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_settings', function (Blueprint $table) {
            $table->unsignedInteger('reservation_rate')->default(500)->after('court_count');
            $table->unsignedInteger('paddle_rent_rate')->default(0)->after('reservation_rate');
            $table->unsignedInteger('ball_rate')->default(0)->after('paddle_rent_rate');
        });
    }

    public function down(): void
    {
        Schema::table('facility_settings', function (Blueprint $table) {
            $table->dropColumn([
                'reservation_rate',
                'paddle_rent_rate',
                'ball_rate',
            ]);
        });
    }
};
