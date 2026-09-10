@extends('layouts.app')

@section('title', 'Master Suara & Audio Bel')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Header & Action -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-family: 'Outfit'; font-size: 1.6rem; font-weight: 700; color: #fff;">
                Koleksi Suara & Rekaman Bel
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Daftar rekaman audio bilingual (Indonesia & English) serta unggahan audio kustom sekolah
            </p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openUploadModal()">
            <i data-lucide="upload"></i> Unggah Suara Kustom
        </button>
    </div>

    <!-- Sound Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
        @foreach($sounds as $s)
            <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between; gap: 16px; border-color: rgba(255, 255, 255, 0.08);">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="background: {{ $s->language === 'id' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(59, 130, 246, 0.2)' }}; color: {{ $s->language === 'id' ? '#fca5a5' : '#93c5fd' }}; border: 1px solid {{ $s->language === 'id' ? 'rgba(239,68,68,0.3)' : 'rgba(59,130,246,0.3)' }}; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                            {{ $s->language === 'id' ? '🇮🇩 INDONESIA' : '🇬🇧 ENGLISH' }}
                        </span>

                        @if($s->is_system_default)
                            <span style="font-size: 0.75rem; color: var(--text-muted); background: rgba(255, 255, 255, 0.05); padding: 3px 8px; border-radius: 4px;">
                                Default Sistem
                            </span>
                        @else
                            <span style="font-size: 0.75rem; color: #34d399; background: rgba(16, 185, 129, 0.1); padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                                Kustom
                            </span>
                        @endif
                    </div>

                    <h3 style="font-family: 'Outfit'; font-size: 1.15rem; font-weight: 700; color: #fff;">
                        {{ $s->name }}
                    </h3>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; font-family: monospace; word-break: break-all;">
                        {{ $s->file_path }}
                    </div>
                </div>

                <!-- Waveform decoration & Play Control -->
                <div style="background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.05); padding: 12px 16px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 4px; height: 24px;">
                        <span style="width: 3px; height: 12px; background: #6366f1; border-radius: 2px;"></span>
                        <span style="width: 3px; height: 20px; background: #6366f1; border-radius: 2px;"></span>
                        <span style="width: 3px; height: 16px; background: #6366f1; border-radius: 2px;"></span>
                        <span style="width: 3px; height: 24px; background: #818cf8; border-radius: 2px;"></span>
                        <span style="width: 3px; height: 14px; background: #6366f1; border-radius: 2px;"></span>
                        <span style="width: 3px; height: 18px; background: #818cf8; border-radius: 2px;"></span>
                        <span style="width: 3px; height: 10px; background: #6366f1; border-radius: 2px;"></span>
                        <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 8px;">{{ $s->duration_sec }}s</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.82rem;" onclick="playAudioUrl('{{ asset($s->file_path) }}', 'Pratinjau Suara', '{{ $s->name }}')">
                            <i data-lucide="play" style="width: 14px; height: 14px;"></i> Putar
                        </button>

                        @if(!$s->is_system_default)
                            <form action="{{ route('sounds.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus suara kustom ini?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="padding: 6px 10px; color: #f43f5e;" title="Hapus">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Modal Upload Custom Sound -->
<div class="modal-overlay" id="modal-upload-sound">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fff;">
                Unggah File Suara Kustom
            </h3>
            <button onclick="closeUploadModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer;"><i data-lucide="x"></i></button>
        </div>

        <form method="POST" action="{{ route('sounds.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Nama Suara / Panggilan</label>
                <input type="text" name="name" required placeholder="Contoh: Bel Khusus Upacara Bendera" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Bahasa Audio</label>
                <select name="language" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                    <option value="id">Bahasa Indonesia</option>
                    <option value="en">English (Bahasa Inggris)</option>
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Pilih File Audio (Format: .mp3, .wav - Maks. 10MB)</label>
                <input type="file" name="audio_file" accept=".mp3,.wav,.ogg,.m4a" required style="width: 100%; padding: 10px; background: #1f2937; border: 1px dashed var(--border-hover); border-radius: 10px; color: #cbd5e1; font-size: 0.85rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeUploadModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Unggah Suara</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openUploadModal() {
        document.getElementById('modal-upload-sound').classList.add('active');
    }

    function closeUploadModal() {
        document.getElementById('modal-upload-sound').classList.remove('active');
    }
</script>
@endsection
