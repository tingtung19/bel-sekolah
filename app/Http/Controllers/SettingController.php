<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Sound;
use App\Models\SpecialSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'school_name' => Setting::get('school_name', 'Sistem Bel Sekolah Digital'),
            'master_volume' => Setting::get('master_volume', '1.0'),
            'pin_code' => Setting::get('pin_code', '1234'),
            'default_language' => Setting::get('default_language', 'id'),
            'mute_all' => Setting::get('mute_all', '0'),
        ];

        return view('pages.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:150',
            'master_volume' => 'required|numeric|min:0|max:1',
            'pin_code' => 'required|string|min:4|max:8',
            'default_language' => 'required|in:id,en',
        ]);

        Setting::set('school_name', $request->school_name);
        Setting::set('master_volume', $request->master_volume);
        Setting::set('pin_code', $request->pin_code);
        Setting::set('default_language', $request->default_language);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan!');
    }

    /**
     * Backup / Ekspor Konfigurasi ke File JSON
     */
    public function exportJson()
    {
        $data = [
            'app_version' => '1.0',
            'exported_at' => now()->toDateTimeString(),
            'settings' => Setting::all(),
            'schedules' => Schedule::all(),
            'holidays' => Holiday::all(),
            'special_schedules' => SpecialSchedule::all(),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT);
        $filename = 'backup_bel_sekolah_' . date('Y_m_d_His') . '.json';

        return Response::make($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Restore / Impor Konfigurasi dari File JSON
     */
    public function importJson(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimetypes:application/json,text/plain|max:5120',
        ]);

        $content = file_get_contents($request->file('backup_file')->getRealPath());
        $data = json_decode($content, true);

        if (!$data || !isset($data['schedules'])) {
            return redirect()->route('settings.index')->with('error', 'Format file backup tidak valid!');
        }

        // Restore Schedules
        if (!empty($data['schedules'])) {
            Schedule::truncate();
            foreach ($data['schedules'] as $item) {
                unset($item['id']);
                Schedule::create($item);
            }
        }

        // Restore Holidays
        if (!empty($data['holidays'])) {
            Holiday::truncate();
            foreach ($data['holidays'] as $item) {
                unset($item['id']);
                Holiday::create($item);
            }
        }

        return redirect()->route('settings.index')->with('success', 'Data konfigurasi berhasil dipulihkan dari backup!');
    }

    /**
     * API Verifikasi PIN
     */
    public function verifyPin(Request $request)
    {
        $inputPin = $request->input('pin');
        $storedPin = Setting::get('pin_code', '1234');

        if ($inputPin === $storedPin) {
            session(['pin_authenticated' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'PIN salah!'], 403);
    }
}
