@extends('layouts.app')

@section('title', 'Riwayat Log Bel')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Header & Action -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-family: 'Outfit'; font-size: 1.6rem; font-weight: 700; color: #fff;">
                Riwayat & Log Bunyi Bel
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Catatan audit otomatis seluruh alarm bel yang telah berbunyi atau dipicu secara manual
            </p>
        </div>
        @if($logs->isNotEmpty())
            <form action="{{ route('logs.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua riwayat catatan bel?');">
                @csrf
                <button type="submit" class="btn btn-secondary" style="color: #f43f5e; border-color: rgba(244, 63, 94, 0.3);">
                    <i data-lucide="trash-2"></i> Bersihkan Semua Log
                </button>
            </form>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="glass-card" style="padding: 16px 20px;">
        <form method="GET" action="{{ route('logs.index') }}" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Filter Tanggal:</label>
                <input type="date" name="date" value="{{ request('date') }}" style="padding: 8px 12px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 0.85rem;">
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Sumber:</label>
                <select name="source_type" style="padding: 8px 12px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 0.85rem;">
                    <option value="">Semua Sumber</option>
                    <option value="regular" {{ request('source_type') === 'regular' ? 'selected' : '' }}>Otomatis Reguler</option>
                    <option value="special" {{ request('source_type') === 'special' ? 'selected' : '' }}>Khusus / Ujian</option>
                    <option value="manual" {{ request('source_type') === 'manual' ? 'selected' : '' }}>Manual Operator</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.85rem;">
                <i data-lucide="filter"></i> Terapkan Filter
            </button>
            @if(request()->hasAny(['date', 'source_type']))
                <a href="{{ route('logs.index') }}" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.85rem;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.04); border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                    <th style="padding: 14px 20px; font-weight: 600;">Waktu Eksekusi</th>
                    <th style="padding: 14px 20px; font-weight: 600;">Label Bel</th>
                    <th style="padding: 14px 20px; font-weight: 600;">Suara & Bahasa</th>
                    <th style="padding: 14px 20px; font-weight: 600;">Sumber Pemicu</th>
                    <th style="padding: 14px 20px; font-weight: 600; text-align: center;">Status</th>
                    <th style="padding: 14px 20px; font-weight: 600;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px 20px;">
                            <div style="font-family: 'Outfit'; font-weight: 700; color: #fff; font-size: 1rem;">
                                {{ $log->triggered_at->format('H:i:s') }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $log->triggered_at->locale('id')->isoFormat('D MMMM Y') }}
                            </div>
                        </td>
                        <td style="padding: 16px 20px; font-weight: 600; color: #f1f5f9;">
                            {{ $log->label }}
                        </td>
                        <td style="padding: 16px 20px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="background: rgba(255, 255, 255, 0.08); padding: 2px 6px; border-radius: 4px; font-size: 0.72rem; font-weight: 700;">
                                    {{ strtoupper($log->language) }}
                                </span>
                                <span style="color: #cbd5e1; font-size: 0.82rem;">{{ $log->sound_name ?? '-' }}</span>
                            </div>
                        </td>
                        <td style="padding: 16px 20px;">
                            @if($log->source_type === 'manual')
                                <span style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                    Manual
                                </span>
                            @elseif($log->source_type === 'special')
                                <span style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: #a5b4fc; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                    Khusus/Ujian
                                </span>
                            @else
                                <span style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                    Otomatis
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px 20px; text-align: center;">
                            <span style="color: #10b981; font-weight: 700; font-size: 0.8rem; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 12px;">
                                Sukses
                            </span>
                        </td>
                        <td style="padding: 16px 20px; color: var(--text-muted); font-size: 0.82rem;">
                            {{ $log->notes ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Belum ada riwayat catatan bel yang terekam.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
