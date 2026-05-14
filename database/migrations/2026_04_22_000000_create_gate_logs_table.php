<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gate_logs', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_key')->nullable()->unique();
            $table->string('plate_number', 32)->index();
            $table->unsignedTinyInteger('gate_number')->index();
            $table->enum('direction', ['in', 'out'])->index();
            $table->timestamp('logged_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_logs');
    }
};
