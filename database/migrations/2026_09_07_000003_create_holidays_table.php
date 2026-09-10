<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable(); // Tanggal spesifik (contoh: 2026-08-17)
            $table->boolean('is_recurring_weekly')->default(false); // Jika berulang tiap minggu
            $table->string('day_of_week', 10)->nullable(); // Contoh: 'Sun'
            $table->string('description'); // Contoh: 'Hari Minggu', 'Hari Kemerdekaan RI'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
