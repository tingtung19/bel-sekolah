@extends('layouts.app')

@section('title', 'Kelola Hari Libur')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Header & Action -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-family: 'Outfit'; font-size: 1.6rem; font-weight: 700; color: #fff;">
                Hari Libur Sekolah
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Tandai tanggal atau hari tertentu sebagai libur agar seluruh alarm bel reguler otomatis tidak berbunyi
            </p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddHolidayModal()">
            <i data-lucide="calendar-plus"></i> Tambah Hari Libur
        </button>
    </div>

    <!-- Cards Grid: Weekly vs Specific -->
    <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px;">
        
        <!-- Libur Mingguan Rutin -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <i data-lucide="repeat" style="color: #a5b4fc; width: 20px; height: 20px;"></i>
                <h3 style="font-family: 'Outfit'; font-size: 1.15rem; font-weight: 700; color: #fff;">
                    Libur Mingguan Rutin
                </h3>
            </div>
            <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 18px;">
                Hari dalam seminggu yang otomatis selalu libur (contoh: hari Minggu).
            </p>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @php
                    $weeklyHolidays = $holidays->where('is_recurring_weekly', true);
                    $dayMap = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                @endphp

                @forelse($weeklyHolidays as $wh)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: rgba(255, 255, 255, 0.04); border-radius: 10px; border: 1px solid var(--border-color);">
                        <div>
                            <div style="font-weight: 700; color: #f43f5e; font-size: 0.95rem;">
                                Semua Hari {{ $dayMap[$wh->day_of_week] ?? $wh->day_of_week }}
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">{{ $wh->description }}</div>
                        </div>
                        <form action="{{ route('holidays.destroy', $wh->id) }}" method="POST" onsubmit="return confirm('Hapus libur mingguan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary" style="padding: 6px 10px; color: #f43f5e;" title="Hapus">
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 0.85rem;">
                        Belum ada libur mingguan yang diset.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Libur Tanggal Kalender Spesifik -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <i data-lucide="calendar" style="color: #6ee7b7; width: 20px; height: 20px;"></i>
                <h3 style="font-family: 'Outfit'; font-size: 1.15rem; font-weight: 700; color: #fff;">
                    Libur Tanggal Spesifik (Kalender Nasional & Sekolah)
                </h3>
            </div>
            <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 18px;">
                Daftar tanggal khusus hari libur (cuti bersama, hari raya, libur akhir semester).
            </p>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @php
                    $specificHolidays = $holidays->where('is_recurring_weekly', false)->sortBy('date');
                @endphp

                @forelse($specificHolidays as $sh)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: rgba(255, 255, 255, 0.04); border-radius: 12px; border: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="text-align: center; background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3); padding: 6px 12px; border-radius: 8px; min-width: 80px;">
                                <div style="font-size: 0.72rem; text-transform: uppercase; color: #fca5a5; font-weight: 700;">
                                    {{ \Carbon\Carbon::parse($sh->date)->locale('id')->isoFormat('MMM') }}
                                </div>
                                <div style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; color: #fff;">
                                    {{ \Carbon\Carbon::parse($sh->date)->format('d') }}
                                </div>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">
                                    {{ $sh->description }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                    {{ \Carbon\Carbon::parse($sh->date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('holidays.destroy', $sh->id) }}" method="POST" onsubmit="return confirm('Hapus hari libur ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary" style="padding: 6px 10px; color: #f43f5e;" title="Hapus">
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div style="text-align: center; padding: 36px; color: var(--text-muted); font-size: 0.88rem;">
                        Belum ada tanggal libur kalender yang ditambahkan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Modal Tambah Libur -->
<div class="modal-overlay" id="modal-holiday-form">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fff;">
                Tambah Hari Libur
            </h3>
            <button onclick="closeAddHolidayModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer;"><i data-lucide="x"></i></button>
        </div>

        <form method="POST" action="{{ route('holidays.store') }}">
            @csrf

            <!-- Tipe Libur -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Tipe Hari Libur</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; color: #fff; font-size: 0.85rem;">
                        <input type="radio" name="holiday_type" value="specific" checked onchange="toggleHolidayInputs('specific')"> Tanggal Kalender
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; color: #fff; font-size: 0.85rem;">
                        <input type="radio" name="holiday_type" value="recurring" onchange="toggleHolidayInputs('recurring')"> Mingguan Rutin
                    </label>
                </div>
            </div>

            <!-- Input Tanggal Spesifik -->
            <div id="wrapper-specific-date" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Pilih Tanggal</label>
                <input type="date" name="date" id="holiday-date" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
            </div>

            <!-- Input Hari Mingguan -->
            <div id="wrapper-recurring-day" style="margin-bottom: 16px; display: none;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Pilih Hari Mingguan</label>
                <select name="day_of_week" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                    <option value="Sun">Semua Hari Minggu</option>
                    <option value="Sat">Semua Hari Sabtu</option>
                    <option value="Fri">Semua Hari Jumat</option>
                </select>
            </div>

            <!-- Keterangan -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Nama / Keterangan Libur</label>
                <input type="text" name="description" required placeholder="Contoh: Libur Hari Raya Idul Fitri / Libur Semester" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeAddHolidayModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Hari Libur</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openAddHolidayModal() {
        document.getElementById('modal-holiday-form').classList.add('active');
    }

    function closeAddHolidayModal() {
        document.getElementById('modal-holiday-form').classList.remove('active');
    }

    function toggleHolidayInputs(type) {
        if (type === 'specific') {
            document.getElementById('wrapper-specific-date').style.display = 'block';
            document.getElementById('wrapper-recurring-day').style.display = 'none';
        } else {
            document.getElementById('wrapper-specific-date').style.display = 'none';
            document.getElementById('wrapper-recurring-day').style.display = 'block';
        }
    }
</script>
@endsection
