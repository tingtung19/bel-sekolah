@extends('layouts.app')

@section('title', 'Pengaturan & Keamanan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 960px;">

    <!-- Header -->
    <div>
        <h2 style="font-family: 'Outfit'; font-size: 1.6rem; font-weight: 700; color: #fff;">
            Pengaturan Sistem & Keamanan
        </h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
            Konfigurasi volume speaker, identitas sekolah, kode PIN pengaman, dan pencadangan data
        </p>
    </div>

    <!-- Form Pengaturan Umum -->
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        <div class="glass-card" style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 24px;">
            <h3 style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 700; color: #fff; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Konfigurasi Umum & Suara
            </h3>

            <!-- Nama Sekolah -->
            <div>
                <label style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Nama Sekolah / Institusi</label>
                <input type="text" name="school_name" required value="{{ $settings['school_name'] }}" style="width: 100%; padding: 12px 16px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.95rem;">
            </div>

            <!-- Master Volume Slider -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #cbd5e1;">Master Volume Suara Bel</label>
                    <span style="font-family: 'Outfit'; font-weight: 700; color: #6366f1; font-size: 1.1rem;" id="volume-label">
                        {{ round($settings['master_volume'] * 100) }}%
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <i data-lucide="volume-1" style="color: var(--text-muted);"></i>
                    <input type="range" name="master_volume" id="volume-range" min="0" max="1" step="0.05" value="{{ $settings['master_volume'] }}" style="flex: 1; accent-color: #6366f1; height: 6px; cursor: pointer;" oninput="updateVolumePreview(this.value)">
                    <i data-lucide="volume-2" style="color: #6366f1;"></i>
                    <button type="button" class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.8rem;" onclick="testVolumeTone()">
                        <i data-lucide="play" style="width: 12px; height: 12px;"></i> Uji Suara
                    </button>
                </div>
            </div>

            <!-- Bahasa Default & PIN -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Bahasa Utama Pengumuman</label>
                    <select name="default_language" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                        <option value="id" {{ $settings['default_language'] === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                        <option value="en" {{ $settings['default_language'] === 'en' ? 'selected' : '' }}>English (Bahasa Inggris)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Kode PIN Akses Admin (4-8 Digit)</label>
                    <input type="password" name="pin_code" required value="{{ $settings['pin_code'] }}" maxlength="8" style="width: 100%; padding: 12px 16px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.95rem; letter-spacing: 0.2em;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; padding-top: 12px; border-top: 1px solid var(--border-color);">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>

    <!-- Backup & Restore Section -->
    <div class="glass-card" style="display: flex; flex-direction: column; gap: 20px;">
        <h3 style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 700; color: #fff; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            Pencadangan & Pemulihan Data (Backup / Restore)
        </h3>
        
        <p style="color: var(--text-muted); font-size: 0.88rem;">
            Simpan seluruh jadwal dan hari libur ke dalam satu file berkas JSON agar dapat dipulihkan dengan mudah jika PC sekolah diinstal ulang.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Export Card -->
            <div style="background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-color); padding: 20px; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between; gap: 16px;">
                <div>
                    <div style="font-weight: 700; color: #fff; font-size: 1rem; margin-bottom: 6px;">Ekspor File Cadangan</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">Unduh seluruh database jadwal, konfigurasi, dan hari libur ke komputer.</div>
                </div>
                <a href="{{ route('settings.export') }}" class="btn btn-secondary" style="justify-content: center;">
                    <i data-lucide="download"></i> Unduh File Backup JSON
                </a>
            </div>

            <!-- Import Card -->
            <div style="background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-color); padding: 20px; border-radius: 12px;">
                <div style="font-weight: 700; color: #fff; font-size: 1rem; margin-bottom: 6px;">Pulihkan Data (Restore)</div>
                <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 14px;">Pilih file JSON backup yang pernah diunduh sebelumnya.</div>

                <form method="POST" action="{{ route('settings.import') }}" enctype="multipart/form-data" onsubmit="return confirm('Peringatan: Memulihkan data akan menggantikan seluruh jadwal saat ini. Lanjutkan?');">
                    @csrf
                    <div style="display: flex; gap: 8px;">
                        <input type="file" name="backup_file" accept=".json" required style="flex: 1; padding: 8px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.8rem; color: #cbd5e1;">
                        <button type="submit" class="btn btn-primary" style="padding: 8px 14px; font-size: 0.82rem;">
                            <i data-lucide="upload-cloud"></i> Pulihkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function updateVolumePreview(val) {
        document.getElementById('volume-label').textContent = Math.round(val * 100) + '%';
        masterVolume = parseFloat(val);
    }

    function testVolumeTone() {
        const val = document.getElementById('volume-range').value;
        masterVolume = parseFloat(val);
        playAudioUrl('{{ asset("audio/id/chime.wav") }}', 'Uji Volume Speaker', Math.round(val * 100) + '%');
    }
</script>
@endsection
