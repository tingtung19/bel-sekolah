<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date')->get();
        return view('pages.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'holiday_type' => 'required|in:specific,recurring',
            'date' => 'nullable|required_if:holiday_type,specific|date',
            'day_of_week' => 'nullable|required_if:holiday_type,recurring|in:Sun,Mon,Tue,Wed,Thu,Fri,Sat',
            'description' => 'required|string|max:150',
        ]);

        if ($validated['holiday_type'] === 'recurring') {
            Holiday::create([
                'is_recurring_weekly' => true,
                'day_of_week' => $validated['day_of_week'],
                'date' => null,
                'description' => $validated['description'],
            ]);
        } else {
            Holiday::create([
                'is_recurring_weekly' => false,
                'day_of_week' => null,
                'date' => $validated['date'],
                'description' => $validated['description'],
            ]);
        }

        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil ditambahkan!');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil dihapus!');
    }
}
