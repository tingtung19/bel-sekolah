<?php

use App\Http\Controllers\BellLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SoundController;
use App\Http\Controllers\SpecialScheduleController;
use Illuminate\Support\Facades\Route;

// Dashboard Utama
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Real-time API untuk Polling Status & Trigger Audio Player
Route::get('/api/status', [DashboardController::class, 'apiStatus'])->name('api.status');
Route::post('/api/trigger-manual', [DashboardController::class, 'triggerManual'])->name('api.trigger-manual');
Route::post('/api/toggle-mute', [DashboardController::class, 'toggleMute'])->name('api.toggle-mute');
Route::post('/api/verify-pin', [SettingController::class, 'verifyPin'])->name('api.verify-pin');

// Manajemen Jadwal Reguler
Route::resource('schedules', ScheduleController::class)->except(['create', 'show', 'edit']);
Route::post('/schedules/{schedule}/toggle', [ScheduleController::class, 'toggle'])->name('schedules.toggle');

// Manajemen Hari Libur
Route::resource('holidays', HolidayController::class)->only(['index', 'store', 'destroy']);

// Manajemen Jadwal Tanggal Khusus (Override)
Route::resource('special-schedules', SpecialScheduleController::class)->only(['index', 'store', 'destroy'])->names('special');

// Master Suara Audio
Route::resource('sounds', SoundController::class)->only(['index', 'store', 'destroy']);

// Pengaturan & Backup/Restore
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
Route::get('/settings/export', [SettingController::class, 'exportJson'])->name('settings.export');
Route::post('/settings/import', [SettingController::class, 'importJson'])->name('settings.import');

// Riwayat Log Bel
Route::get('/logs', [BellLogController::class, 'index'])->name('logs.index');
Route::post('/logs/clear', [BellLogController::class, 'clear'])->name('logs.clear');
