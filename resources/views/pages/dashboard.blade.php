@extends('layouts.app')

@section('title', 'Dashboard - ' . $schoolName)

@section('content')
<div style="display: flex; flex-direction: column; gap: 28px;">

    <!-- Hero Section: Status & Next Bell Spotlight -->
    <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px;">
        <!-- Status Operasional Card -->
        <div class="glass-card" style="position: relative; overflow: hidden; background: linear-gradient(135deg, rgba(30, 27, 75, 0.8), rgba(15, 23, 42, 0.85)); border-color: rgba(99, 102, 241, 0.25);">
            <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.08; pointer-events: none;">
                <i data-lucide="bell" style="width: 220px; height: 220px; color: #fff;"></i>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: #a5b4fc; font-weight: 700;">
                    Status Operasional Sekolah
                </div>
                <div>
                    @if($statusType === 'holiday')
                        <span style="background: rgba(244, 63, 94, 0.2); border: 1px solid rgba(244, 63, 94, 0.4); color: #fb7185; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                            HARI LIBUR
                        </span>
                    @elseif($statusType === 'special')
                        <span style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                            JADWAL KHUSUS / UJIAN
                        </span>
                    @else
                        <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                            JADWAL REGULER AKTIF
                        </span>
                    @endif
                </div>
            </div>

            <h2 style="font-family: 'Outfit'; font-size: 1.65rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                {{ $schoolName }}
            </h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px;">
                {{ $statusMessage }}
            </p>

            <div style="display: flex; gap: 20px; border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 18px;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total Jadwal Hari Ini</div>
                    <div style="font-size: 1.4rem; font-weight: 700; color: #fff;" id="stat-total-today">{{ count($schedules) }}</div>
                </div>
                <div style="border-left: 1px solid rgba(255, 255, 255, 0.08); padding-left: 20px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Sudah Berbunyi</div>
                    <div style="font-size: 1.4rem; font-weight: 700; color: #10b981;" id="stat-passed-today">
                        {{ count(array_filter($schedules, fn($s) => $s['is_passed'])) }}
                    </div>
                </div>
                <div style="border-left: 1px solid rgba(255, 255, 255, 0.08); padding-left: 20px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Belum Berbunyi</div>
                    <div style="font-size: 1.4rem; font-weight: 700; color: #6366f1;" id="stat-remaining-today">
                        {{ count(array_filter($schedules, fn($s) => !$s['is_passed'])) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Bell Spotlight Card -->
        <div class="glass-card" style="background: linear-gradient(135deg, rgba(6, 78, 59, 0.3), rgba(15, 23, 42, 0.85)); border-color: rgba(16, 185, 129, 0.3); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: #6ee7b7; font-weight: 700;">
                        Alarm Berikutnya
                    </span>
                    <i data-lucide="alarm-clock" style="color: #34d399; width: 22px; height: 22px;"></i>
                </div>

                @if($nextBell)
                    <div style="font-family: 'Outfit'; font-size: 3rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; line-height: 1;" id="spotlight-bell-time">
                        {{ $nextBell['time'] }}
                    </div>
                    <div style="font-size: 1.15rem; font-weight: 600; color: #e2e8f0; margin-top: 6px;" id="spotlight-bell-label">
                        {{ $nextBell['label'] }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                        <span style="background: rgba(255, 255, 255, 0.1); padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                            {{ strtoupper($nextBell['language']) }}
                        </span>
                        <span style="color: var(--text-muted); font-size: 0.85rem;" id="spotlight-bell-sound">
                            {{ $nextBell['sound_name'] }}
                        </span>
                    </div>
                @else
                    <div style="padding: 20px 0; color: var(--text-muted);">
                        <i data-lucide="check-circle-2" style="width: 36px; height: 36px; color: #10b981; margin-bottom: 8px;"></i>
                        <div style="font-size: 1.1rem; font-weight: 600; color: #fff;">Semua bel hari ini telah selesai!</div>
                        <p style="font-size: 0.85rem; margin-top: 4px;">Tidak ada alarm yang tersisa untuk hari ini.</p>
                    </div>
                @endif
            </div>

            <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.82rem; color: var(--text-muted);">Sinkronisasi otomatis</span>
                <button type="button" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;" onclick="location.reload()">
                    <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Timeline Jadwal Hari Ini -->
    <div class="glass-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
            <div>
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fff;">
                    Rangkaian Jadwal Bel Hari Ini
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 2px;">
                    Daftar seluruh bunyi alarm yang aktif dan diatur untuk berbunyi sepanjang hari ini
                </p>
            </div>
            <a href="{{ route('schedules.index') }}" class="btn btn-secondary" style="font-size: 0.82rem;">
                <i data-lucide="settings-2"></i> Kelola Semua Jadwal
            </a>
        </div>

        @if(empty($schedules))
            <div style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                <i data-lucide="calendar-x" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 12px; opacity: 0.5;"></i>
                <div style="font-size: 1.1rem; font-weight: 600; color: #fff;">Tidak Ada Jadwal Aktif</div>
                <p style="font-size: 0.88rem; max-width: 420px; margin: 6px auto 18px;">
                    Hari ini ditandai sebagai hari libur atau belum ada jadwal reguler yang diatur untuk hari ini.
                </p>
                <a href="{{ route('schedules.index') }}" class="btn btn-primary">Tambah Jadwal Baru</a>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;" id="schedule-timeline-container">
                @foreach($schedules as $item)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: {{ $item['is_passed'] ? 'rgba(255, 255, 255, 0.02)' : 'rgba(255, 255, 255, 0.05)' }}; border: 1px solid {{ $item['is_passed'] ? 'transparent' : 'rgba(255, 255, 255, 0.08)' }}; border-radius: 14px; transition: all 0.2s;" id="schedule-row-{{ $item['id'] }}">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <!-- Time Badge -->
                            <div style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 800; color: {{ $item['is_passed'] ? 'var(--text-muted)' : '#fff' }}; min-width: 70px;">
                                {{ $item['time'] }}
                            </div>

                            <!-- Label & Details -->
                            <div>
                                <div style="font-weight: 700; font-size: 1rem; color: {{ $item['is_passed'] ? 'var(--text-muted)' : '#fff' }};">
                                    {{ $item['label'] }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px; margin-top: 4px; font-size: 0.8rem; color: var(--text-muted);">
                                    <span><i data-lucide="music" style="width: 12px; height: 12px; vertical-align: middle;"></i> {{ $item['sound_name'] }}</span>
                                    <span>•</span>
                                    <span style="background: rgba(255,255,255,0.08); padding: 2px 6px; border-radius: 4px; font-weight: 600;">
                                        {{ strtoupper($item['language']) }}
                                    </span>
                                    @if($item['source_type'] === 'special')
                                        <span style="color: #fbbf24; font-weight: 600;">[Khusus/Ujian]</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status & Action -->
                        <div style="display: flex; align-items: center; gap: 14px;">
                            @if($item['is_passed'])
                                <span style="display: flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 600; color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 6px 12px; border-radius: 20px;">
                                    <i data-lucide="check" style="width: 14px; height: 14px;"></i> Selesai
                                </span>
                            @else
                                <span style="display: flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 600; color: #6366f1; background: rgba(99, 102, 241, 0.15); padding: 6px 12px; border-radius: 20px;">
                                    <i data-lucide="clock" style="width: 14px; height: 14px;"></i> Menunggu
                                </span>
                            @endif

                            <button type="button" class="btn btn-secondary" style="padding: 8px 12px; font-size: 0.8rem;" onclick="playAudioUrl('{{ $item['sound_url'] }}', 'Uji Coba: {{ $item['label'] }}', '{{ $item['sound_name'] }}')" title="Putar Sekarang">
                                <i data-lucide="play" style="width: 14px; height: 14px;"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Live update status hook from layout polling
    window.onStatusUpdate = function(data) {
        if (!data) return;
        
        // Update stats
        if (data.schedules) {
            document.getElementById('stat-total-today').textContent = data.schedules.length;
            const passed = data.schedules.filter(s => s.is_passed).length;
            document.getElementById('stat-passed-today').textContent = passed;
            document.getElementById('stat-remaining-today').textContent = data.schedules.length - passed;
        }

        // Update spotlight next bell
        if (data.next_bell) {
            const timeEl = document.getElementById('spotlight-bell-time');
            const labelEl = document.getElementById('spotlight-bell-label');
            const soundEl = document.getElementById('spotlight-bell-sound');
            if (timeEl) timeEl.textContent = data.next_bell.time;
            if (labelEl) labelEl.textContent = data.next_bell.label;
            if (soundEl) soundEl.textContent = data.next_bell.sound_name;
        }
    };
</script>
@endsection
