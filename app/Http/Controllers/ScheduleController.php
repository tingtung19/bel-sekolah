<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Sound;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('sound')->orderBy('time')->get();
        $sounds = Sound::orderBy('language')->orderBy('name')->get();

        return view('pages.schedules.index', compact('schedules', 'sounds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'time' => 'required|regex:/^\d{2}:\d{2}$/',
            'label' => 'required|string|max:100',
            'days_of_week' => 'required|array|min:1',
            'sound_id' => 'required|exists:sounds,id',
            'language' => 'required|in:id,en',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Schedule::create($validated);

        return redirect()->route('schedules.index')->with('success', 'Jadwal bel berhasil ditambahkan!');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'time' => 'required|regex:/^\d{2}:\d{2}$/',
            'label' => 'required|string|max:100',
            'days_of_week' => 'required|array|min:1',
            'sound_id' => 'required|exists:sounds,id',
            'language' => 'required|in:id,en',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $schedule->update($validated);

        return redirect()->route('schedules.index')->with('success', 'Jadwal bel berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Jadwal bel berhasil dihapus!');
    }

    public function toggle(Schedule $schedule)
    {
        $schedule->is_active = !$schedule->is_active;
        $schedule->save();

        return response()->json([
            'success' => true,
            'is_active' => $schedule->is_active,
            'message' => $schedule->is_active ? 'Jadwal diaktifkan' : 'Jadwal dinonaktifkan',
        ]);
    }
}
