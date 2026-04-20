<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('reschedule_unlocked_at')->nullable()->after('payment_status');
            $table->date('reschedule_deadline')->nullable()->after('reschedule_unlocked_at');
            $table->string('reschedule_reason')->nullable()->after('reschedule_deadline');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'reschedule_unlocked_at',
                'reschedule_deadline',
                'reschedule_reason',
            ]);
        });
    }
};
