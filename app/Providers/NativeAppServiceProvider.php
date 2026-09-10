<?php

namespace App\Providers;

use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\MenuBar;
use Native\Desktop\Facades\Window;
use Native\Desktop\Menu\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // 1. Konfigurasi Window Utama Desktop
        Window::open('main')
            ->title('Sistem Bel Sekolah Otomatis')
            ->width(1280)
            ->height(840)
            ->minWidth(960)
            ->minHeight(650)
            ->rememberState();

        // 2. Konfigurasi System Tray di Taskbar Kanan Bawah
        try {
            MenuBar::create()
                ->tooltip('Bel Sekolah Otomatis - Berjalan di Latar Belakang')
                ->contextMenu(
                    Menu::new()
                        ->label('Sistem Bel Sekolah Otomatis')
                        ->separator()
                        ->link(route('dashboard'), 'Buka Dashboard')
                        ->separator()
                        ->quit()
                );
        } catch (\Throwable $e) {
            // Fallback jika menubar tidak didukung di environment tertentu
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '256M',
            'max_execution_time' => '0',
        ];
    }
}
