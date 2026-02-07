<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->enum('area', ['A', 'B']);
            $table->integer('slot_number');
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->decimal('cost_per_hour', 8, 2)->default(10.00); // Default $10/hr
            $table->timestamps();

            $table->unique(['area', 'slot_number']);
        });

        Schema::create('parking_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parking_slot_id')->constrained('parking_slots')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parking_reservations');
        Schema::dropIfExists('parking_slots');
    }
};
