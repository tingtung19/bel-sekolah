<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bell_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('triggered_at');
            $table->string('source_type', 20)->default('regular'); // 'regular', 'special', 'manual'
            $table->string('label');
            $table->string('sound_name')->nullable();
            $table->string('language', 10)->default('id');
            $table->string('status', 20)->default('success'); // 'success', 'skipped', 'failed'
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bell_logs');
    }
};
