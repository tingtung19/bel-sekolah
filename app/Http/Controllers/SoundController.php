<?php

namespace App\Http\Controllers;

use App\Models\Sound;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SoundController extends Controller
{
    public function index()
    {
        $sounds = Sound::orderBy('language')->orderBy('name')->get();
        return view('pages.sounds.index', compact('sounds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'language' => 'required|in:id,en',
            'audio_file' => 'required|file|mimes:mp3,wav,ogg,m4a|max:10240', // max 10MB
        ]);

        $file = $request->file('audio_file');
        $extension = $file->getClientOriginalExtension();
        $safeName = Str::slug($request->name) . '_' . time() . '.' . $extension;
        
        $destinationPath = public_path('audio/custom');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $safeName);
        $relativePath = 'audio/custom/' . $safeName;

        Sound::create([
            'name' => $request->name,
            'file_path' => $relativePath,
            'language' => $request->language,
            'duration_sec' => 5,
            'is_system_default' => false,
        ]);

        return redirect()->route('sounds.index')->with('success', 'File audio berhasil diunggah!');
    }

    public function destroy(Sound $sound)
    {
        if ($sound->is_system_default) {
            return redirect()->route('sounds.index')->with('error', 'Suara bawaan sistem tidak boleh dihapus!');
        }

        $fullPath = public_path($sound->file_path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $sound->delete();

        return redirect()->route('sounds.index')->with('success', 'File audio berhasil dihapus!');
    }
}
