<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('time', 10); // 'HH:mm' e.g. '07:00'
            $table->string('label'); // 'Masuk Kelas', 'Istirahat', etc.
            $table->json('days_of_week'); // e.g. ["Mon","Tue","Wed","Thu","Fri"]
            $table->foreignId('sound_id')->nullable()->constrained('sounds')->nullOnDelete();
            $table->string('language', 10)->default('id'); // 'id' or 'en'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
