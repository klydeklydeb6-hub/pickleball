<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('court_count')->default(9);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_settings');
    }
};
