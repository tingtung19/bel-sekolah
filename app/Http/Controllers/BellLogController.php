<?php

namespace App\Http\Controllers;

use App\Models\BellLog;
use Illuminate\Http\Request;

class BellLogController extends Controller
{
    public function index(Request $request)
    {
        $query = BellLog::orderBy('triggered_at', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('triggered_at', $request->date);
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        $logs = $query->paginate(25);

        return view('pages.logs.index', compact('logs'));
    }

    public function clear()
    {
        BellLog::truncate();
        return redirect()->route('logs.index')->with('success', 'Semua riwayat bel berhasil dibersihkan!');
    }
}
