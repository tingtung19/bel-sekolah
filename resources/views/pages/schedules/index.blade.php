@extends('layouts.app')

@section('title', 'Kelola Jadwal Bel Reguler')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Header & Action -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-family: 'Outfit'; font-size: 1.6rem; font-weight: 700; color: #fff;">
                Jadwal Bel Reguler
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Pengaturan jam alarm mingguan otomatis (masuk, istirahat, sholat, pulang)
            </p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddModal()">
            <i data-lucide="plus"></i> Tambah Jadwal Baru
        </button>
    </div>

    <!-- Table Card -->
    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.04); border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                    <th style="padding: 16px 24px; font-weight: 600;">Waktu</th>
                    <th style="padding: 16px 20px; font-weight: 600;">Nama Kegiatan / Label</th>
                    <th style="padding: 16px 20px; font-weight: 600;">Hari Aktif</th>
                    <th style="padding: 16px 20px; font-weight: 600;">Suara & Bahasa</th>
                    <th style="padding: 16px 20px; font-weight: 600; text-align: center;">Status</th>
                    <th style="padding: 16px 24px; font-weight: 600; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 18px 24px;">
                            <span style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fff; background: rgba(99, 102, 241, 0.12); padding: 6px 12px; border-radius: 8px; border: 1px solid rgba(99, 102, 241, 0.25);">
                                {{ $s->time }}
                            </span>
                        </td>
                        <td style="padding: 18px 20px; font-weight: 600; color: #f1f5f9;">
                            {{ $s->label }}
                        </td>
                        <td style="padding: 18px 20px;">
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @php
                                    $dayMap = ['Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab', 'Sun' => 'Min'];
                                @endphp
                                @foreach($s->days_of_week ?? [] as $day)
                                    <span style="background: rgba(255, 255, 255, 0.08); padding: 3px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 600; color: #cbd5e1;">
                                        {{ $dayMap[$day] ?? $day }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td style="padding: 18px 20px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: {{ $s->language === 'id' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(59, 130, 246, 0.2)' }}; color: {{ $s->language === 'id' ? '#fca5a5' : '#93c5fd' }}; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                    {{ strtoupper($s->language) }}
                                </span>
                                <span style="color: #cbd5e1; font-size: 0.85rem;">{{ $s->sound?->name ?? 'Default Chime' }}</span>
                                @if($s->sound)
                                    <button type="button" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="playAudioUrl('{{ asset($s->sound->file_path) }}', '{{ $s->label }}', '{{ $s->sound->name }}')">
                                        <i data-lucide="play" style="width: 12px; height: 12px;"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            <button type="button" onclick="toggleScheduleStatus({{ $s->id }}, this)" style="background: {{ $s->is_active ? 'rgba(16, 185, 129, 0.2)' : 'rgba(244, 63, 94, 0.15)' }}; border: 1px solid {{ $s->is_active ? '#10b981' : '#f43f5e' }}; color: {{ $s->is_active ? '#34d399' : '#fb7185' }}; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                                {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td style="padding: 18px 24px; text-align: right;">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                <button type="button" class="btn btn-secondary" style="padding: 6px 10px;" onclick='openEditModal(@json($s))' title="Edit">
                                    <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i>
                                </button>
                                <form action="{{ route('schedules.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="padding: 6px 10px; color: #f43f5e;" title="Hapus">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            Belum ada jadwal yang tersimpan. Klik "Tambah Jadwal Baru" untuk mulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Tambah/Edit Jadwal -->
<div class="modal-overlay" id="modal-schedule-form">
    <div class="modal-box" style="max-width: 580px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fff;" id="modal-schedule-title">
                Tambah Jadwal Bel
            </h3>
            <button onclick="closeScheduleModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer;"><i data-lucide="x"></i></button>
        </div>

        <form id="form-schedule" method="POST" action="{{ route('schedules.store') }}">
            @csrf
            <div id="method-container"></div>

            <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Jam (HH:mm)</label>
                    <input type="time" name="time" id="input-time" required style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 1.1rem; font-weight: 700;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Nama Kegiatan</label>
                    <input type="text" name="label" id="input-label" required placeholder="Contoh: Masuk Kelas / Istirahat" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                </div>
            </div>

            <!-- Hari Berlaku -->
            <div style="margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1;">Hari Berlaku</label>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" class="btn btn-secondary" style="padding: 3px 8px; font-size: 0.72rem;" onclick="setDaysPreset(['Mon','Tue','Wed','Thu','Fri'])">Sen - Jum</button>
                        <button type="button" class="btn btn-secondary" style="padding: 3px 8px; font-size: 0.72rem;" onclick="setDaysPreset(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'])">Semua</button>
                        <button type="button" class="btn btn-secondary" style="padding: 3px 8px; font-size: 0.72rem;" onclick="setDaysPreset([])">Reset</button>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;">
                    @php
                        $days = ['Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab', 'Sun' => 'Min'];
                    @endphp
                    @foreach($days as $key => $name)
                        <label style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 8px 4px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer;">
                            <input type="checkbox" name="days_of_week[]" value="{{ $key }}" class="day-checkbox" style="margin-bottom: 4px;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: #e2e8f0;">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Suara & Bahasa -->
            <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Pilih Suara Bel</label>
                    <select name="sound_id" id="input-sound-id" required style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                        @foreach($sounds as $snd)
                            <option value="{{ $snd->id }}" data-lang="{{ $snd->language }}">[{{ strtoupper($snd->language) }}] {{ $snd->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Bahasa Pengumuman</label>
                    <div style="display: flex; gap: 12px; padding: 10px 0;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #fff; font-size: 0.88rem;">
                            <input type="radio" name="language" value="id" id="lang-id" checked> Indonesia
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #fff; font-size: 0.88rem;">
                            <input type="radio" name="language" value="en" id="lang-en"> English
                        </label>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; font-size: 0.88rem;">
                    <input type="checkbox" name="is_active" id="input-is-active" value="1" checked> Aktifkan jadwal ini
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeScheduleModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-schedule">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setDaysPreset(days) {
        document.querySelectorAll('.day-checkbox').forEach(cb => {
            cb.checked = days.includes(cb.value);
        });
    }

    function openAddModal() {
        document.getElementById('modal-schedule-title').textContent = 'Tambah Jadwal Bel';
        const form = document.getElementById('form-schedule');
        form.action = '{{ route("schedules.store") }}';
        document.getElementById('method-container').innerHTML = '';
        document.getElementById('input-time').value = '07:00';
        document.getElementById('input-label').value = '';
        setDaysPreset(['Mon','Tue','Wed','Thu','Fri']);
        document.getElementById('input-is-active').checked = true;
        document.getElementById('modal-schedule-form').classList.add('active');
    }

    function openEditModal(schedule) {
        document.getElementById('modal-schedule-title').textContent = 'Edit Jadwal Bel';
        const form = document.getElementById('form-schedule');
        form.action = `/schedules/${schedule.id}`;
        document.getElementById('method-container').innerHTML = '@method("PUT")';
        document.getElementById('input-time').value = schedule.time;
        document.getElementById('input-label').value = schedule.label;
        document.getElementById('input-sound-id').value = schedule.sound_id;
        
        if (schedule.language === 'en') {
            document.getElementById('lang-en').checked = true;
        } else {
            document.getElementById('lang-id').checked = true;
        }

        setDaysPreset(schedule.days_of_week || []);
        document.getElementById('input-is-active').checked = !!schedule.is_active;
        document.getElementById('modal-schedule-form').classList.add('active');
    }

    function closeScheduleModal() {
        document.getElementById('modal-schedule-form').classList.remove('active');
    }

    async function toggleScheduleStatus(id, btn) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const res = await fetch(`/schedules/${id}/toggle`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                if (data.is_active) {
                    btn.textContent = 'Aktif';
                    btn.style.background = 'rgba(16, 185, 129, 0.2)';
                    btn.style.borderColor = '#10b981';
                    btn.style.color = '#34d399';
                } else {
                    btn.textContent = 'Nonaktif';
                    btn.style.background = 'rgba(244, 63, 94, 0.15)';
                    btn.style.borderColor = '#f43f5e';
                    btn.style.color = '#fb7185';
                }
            }
        } catch (e) {
            console.error(e);
        }
    }
</script>
@endsection
