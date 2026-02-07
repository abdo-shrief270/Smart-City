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
        Schema::create('smart_farm_data', function (Blueprint $table) {
            $table->id();
            $table->integer('temp')->default(0);
            $table->integer('humidity')->default(0);
            $table->boolean('is_pump_on')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_farm_data');
    }
};
