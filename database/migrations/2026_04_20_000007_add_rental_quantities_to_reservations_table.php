<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedInteger('paddle_rent_quantity')->default(0)->after('players');
            $table->unsignedInteger('ball_quantity')->default(0)->after('paddle_rent_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'paddle_rent_quantity',
                'ball_quantity',
            ]);
        });
    }
};
