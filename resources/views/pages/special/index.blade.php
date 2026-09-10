@extends('layouts.app')

@section('title', 'Jadwal Khusus & Override Ujian')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Header & Action -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-family: 'Outfit'; font-size: 1.6rem; font-weight: 700; color: #fff;">
                Jadwal Khusus & Override Ujian
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Tetapkan jadwal bel spesifik untuk tanggal tertentu (Ujian Semester, Jadwal Ramadhan, Acara Khusus)
            </p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddSpecialModal()">
            <i data-lucide="calendar-plus"></i> Tambah Jadwal Khusus
        </button>
    </div>

    <!-- Info Callout Banner -->
    <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); padding: 16px 20px; border-radius: 14px; display: flex; align-items: center; gap: 14px;">
        <i data-lucide="info" style="color: #fbbf24; width: 24px; height: 24px; flex-shrink: 0;"></i>
        <div style="font-size: 0.88rem; color: #fde68a;">
            <strong>Aturan Override:</strong> Jika opsi <em>"Gantikan jadwal reguler"</em> dicentang pada tanggal ini, sistem hanya akan membunyikan jadwal khusus ini dan mengabaikan seluruh jadwal reguler normal pada hari tersebut.
        </div>
    </div>

    <!-- Table Card -->
    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.04); border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                    <th style="padding: 16px 24px; font-weight: 600;">Tanggal Berlaku</th>
                    <th style="padding: 16px 20px; font-weight: 600;">Waktu</th>
                    <th style="padding: 16px 20px; font-weight: 600;">Label / Acara</th>
                    <th style="padding: 16px 20px; font-weight: 600;">Suara & Bahasa</th>
                    <th style="padding: 16px 20px; font-weight: 600; text-align: center;">Tipe Penggantian</th>
                    <th style="padding: 16px 24px; font-weight: 600; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($specialSchedules as $ss)
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 18px 24px;">
                            <div style="font-weight: 700; color: #fff;">
                                {{ \Carbon\Carbon::parse($ss->date)->locale('id')->isoFormat('dddd, D MMM Y') }}
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $ss->date->format('Y-m-d') }}</div>
                        </td>
                        <td style="padding: 18px 20px;">
                            <span style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fbbf24; background: rgba(245, 158, 11, 0.15); padding: 6px 12px; border-radius: 8px; border: 1px solid rgba(245, 158, 11, 0.3);">
                                {{ $ss->time }}
                            </span>
                        </td>
                        <td style="padding: 18px 20px; font-weight: 600; color: #f1f5f9;">
                            {{ $ss->label }}
                        </td>
                        <td style="padding: 18px 20px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: rgba(255, 255, 255, 0.08); padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                    {{ strtoupper($ss->language) }}
                                </span>
                                <span style="color: #cbd5e1; font-size: 0.85rem;">{{ $ss->sound?->name ?? 'Default Chime' }}</span>
                                @if($ss->sound)
                                    <button type="button" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="playAudioUrl('{{ asset($ss->sound->file_path) }}', '{{ $ss->label }}', '{{ $ss->sound->name }}')">
                                        <i data-lucide="play" style="width: 12px; height: 12px;"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            @if($ss->overrides_regular)
                                <span style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">
                                    Gantikan Reguler
                                </span>
                            @else
                                <span style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #93c5fd; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">
                                    Sisipkan (Tambahan)
                                </span>
                            @endif
                        </td>
                        <td style="padding: 18px 24px; text-align: right;">
                            <form action="{{ route('special.destroy', $ss->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal khusus ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="padding: 6px 10px; color: #f43f5e;" title="Hapus">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            Belum ada jadwal khusus atau override tanggal yang dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Tambah Jadwal Khusus -->
<div class="modal-overlay" id="modal-special-form">
    <div class="modal-box" style="max-width: 540px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fff;">
                Tambah Jadwal Khusus
            </h3>
            <button onclick="closeAddSpecialModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer;"><i data-lucide="x"></i></button>
        </div>

        <form method="POST" action="{{ route('special.store') }}">
            @csrf

            <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Tanggal Khusus</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Jam (HH:mm)</label>
                    <input type="time" name="time" required value="08:00" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 1.1rem; font-weight: 700;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Nama Acara / Ujian</label>
                <input type="text" name="label" required placeholder="Contoh: Mulai Ujian Tengah Semester (Jam 1)" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Pilih Suara Bel</label>
                    <select name="sound_id" required style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                        @foreach($sounds as $snd)
                            <option value="{{ $snd->id }}">[{{ strtoupper($snd->language) }}] {{ $snd->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Bahasa</label>
                    <div style="display: flex; gap: 12px; padding: 10px 0;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #fff; font-size: 0.88rem;">
                            <input type="radio" name="language" value="id" checked> ID
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #fff; font-size: 0.88rem;">
                            <input type="radio" name="language" value="en"> EN
                        </label>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px; background: rgba(255, 255, 255, 0.04); padding: 14px; border-radius: 10px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 0.88rem;">
                    <input type="checkbox" name="overrides_regular" value="1" checked>
                    <span><strong>Gantikan jadwal reguler</strong> (jadwal reguler normal tidak akan bunyi pada tanggal ini)</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeAddSpecialModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Jadwal Khusus</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openAddSpecialModal() {
        document.getElementById('modal-special-form').classList.add('active');
    }

    function closeAddSpecialModal() {
        document.getElementById('modal-special-form').classList.remove('active');
    }
</script>
@endsection
