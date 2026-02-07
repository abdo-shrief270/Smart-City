<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('smart_tank_data', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->default(0);
            $table->string('status')->default('Unknown');
            $table->boolean('is_pump_on')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_tank_data');
    }
};
