<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up()
{
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->string('customer_name');
        $table->date('booking_date');
        $table->string('time_slot');
        $table->integer('court_number');
        $table->integer('players')->default(4);
        $table->string('payment_method')->default('gcash');
        $table->string('payment_reference')->nullable();
        $table->string('payment_status')->default('Paid');
        $table->decimal('amount', 10, 2)->default(500);
        $table->string('receipt_no')->unique();
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};