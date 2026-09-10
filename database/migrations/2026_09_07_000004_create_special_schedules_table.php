<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Tanggal khusus berlaku
            $table->string('time', 10); // 'HH:mm' e.g. '07:30'
            $table->string('label'); // Contoh: 'Mulai Ujian Tengah Semester'
            $table->foreignId('sound_id')->nullable()->constrained('sounds')->nullOnDelete();
            $table->string('language', 10)->default('id');
            $table->boolean('overrides_regular')->default(true); // Abaikan jadwal reguler pada hari ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_schedules');
    }
};
