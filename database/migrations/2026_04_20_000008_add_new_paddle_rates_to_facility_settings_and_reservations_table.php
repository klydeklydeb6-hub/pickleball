<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_settings', function (Blueprint $table) {
            $table->unsignedInteger('new_paddle_rent_rate')->default(60)->after('paddle_rent_rate');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedInteger('new_paddle_rent_quantity')->default(0)->after('paddle_rent_quantity');
        });

        if (Schema::hasTable('facility_settings')) {
            DB::table('facility_settings')
                ->where('paddle_rent_rate', 0)
                ->update(['paddle_rent_rate' => 50]);

            DB::table('facility_settings')
                ->update(['new_paddle_rent_rate' => 60]);
        }
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('new_paddle_rent_quantity');
        });

        Schema::table('facility_settings', function (Blueprint $table) {
            $table->dropColumn('new_paddle_rent_rate');
        });
    }
};
