<?php

namespace App\Http\Controllers;

use App\Models\Sound;
use App\Models\SpecialSchedule;
use Illuminate\Http\Request;

class SpecialScheduleController extends Controller
{
    public function index()
    {
        $specialSchedules = SpecialSchedule::with('sound')
            ->orderBy('date', 'desc')
            ->orderBy('time')
            ->get();
        $sounds = Sound::orderBy('language')->orderBy('name')->get();

        return view('pages.special.index', compact('specialSchedules', 'sounds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|regex:/^\d{2}:\d{2}$/',
            'label' => 'required|string|max:100',
            'sound_id' => 'required|exists:sounds,id',
            'language' => 'required|in:id,en',
            'overrides_regular' => 'nullable|boolean',
        ]);

        $validated['overrides_regular'] = $request->has('overrides_regular');

        SpecialSchedule::create($validated);

        return redirect()->route('special.index')->with('success', 'Jadwal khusus tanggal berhasil ditambahkan!');
    }

    public function destroy(SpecialSchedule $specialSchedule)
    {
        $specialSchedule->delete();
        return redirect()->route('special.index')->with('success', 'Jadwal khusus berhasil dihapus!');
    }
}
