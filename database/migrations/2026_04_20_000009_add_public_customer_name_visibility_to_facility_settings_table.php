<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_settings', function (Blueprint $table) {
            $table->boolean('show_public_customer_names')
                ->default(false)
                ->after('ball_rate');
        });
    }

    public function down(): void
    {
        Schema::table('facility_settings', function (Blueprint $table) {
            $table->dropColumn('show_public_customer_names');
        });
    }
};
