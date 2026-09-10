<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Bel Sekolah Otomatis')</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --bg-base: #0a0f1d;
            --bg-surface: #111827;
            --bg-card: rgba(17, 24, 39, 0.75);
            --bg-card-hover: rgba(31, 41, 55, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(99, 102, 241, 0.4);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.35);
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --accent-cyan: #06b6d4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 85%, rgba(16, 185, 129, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(6, 182, 212, 0.05) 0%, transparent 50%);
            background-attachment: fixed;
            overflow-x: hidden;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 280px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px var(--primary-glow);
            color: white;
        }

        .brand-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #fff;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .sidebar-nav {
            padding: 20px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .nav-item:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.05);
            transform: translateX(3px);
        }

        .nav-item.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.2), rgba(99, 102, 241, 0.05));
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .nav-item.active i {
            color: var(--primary);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            background: rgba(10, 15, 29, 0.6);
        }

        .status-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.04);
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-emerald);
            box-shadow: 0 0 12px var(--accent-emerald);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Main Content Area */
        .main-wrapper {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-navbar {
            height: 76px;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            background: rgba(10, 15, 29, 0.6);
            backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .clock-display {
            display: flex;
            align-items: baseline;
            gap: 12px;
        }

        .clock-time {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #ffffff 30%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
        }

        .clock-date {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            box-shadow: 0 6px 16px var(--primary-glow);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px var(--primary-glow);
        }

        .btn-danger {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            color: #fff;
            box-shadow: 0 6px 16px rgba(244, 63, 94, 0.35);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(244, 63, 94, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
            border-color: var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.15);
        }

        .btn-muted {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .content-area {
            padding: 32px;
            flex: 1;
        }

        /* Card System */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.25s ease;
        }

        .glass-card:hover {
            border-color: var(--border-hover);
        }

        /* Modal Dialog */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #111827;
            border: 1px solid var(--border-hover);
            border-radius: 20px;
            width: 100%;
            max-width: 520px;
            padding: 28px;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.6);
            animation: modal-pop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modal-pop {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Toast Alert */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 200;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast-bell {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border: 1px solid #6366f1;
            padding: 16px 20px;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            gap: 14px;
            color: #fff;
            min-width: 320px;
            animation: toast-in 0.3s ease;
        }

        @keyframes toast-in {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Custom Audio Visualizer Ring */
        .bell-ring-active {
            animation: bell-bounce 0.6s infinite alternate;
        }

        @keyframes bell-bounce {
            from { transform: rotate(-15deg); }
            to { transform: rotate(15deg); }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i data-lucide="bell-ring" id="sidebar-bell-icon"></i>
            </div>
            <div class="brand-text">
                <h1>Bel Sekolah</h1>
                <p>Otomatis & Bilingual</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" id="nav-dashboard">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('schedules.index') }}" class="nav-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}" id="nav-schedules">
                <i data-lucide="clock"></i>
                <span>Jadwal Reguler</span>
            </a>
            <a href="{{ route('holidays.index') }}" class="nav-item {{ request()->routeIs('holidays.*') ? 'active' : '' }}" id="nav-holidays">
                <i data-lucide="calendar-off"></i>
                <span>Hari Libur</span>
            </a>
            <a href="{{ route('special.index') }}" class="nav-item {{ request()->routeIs('special.*') ? 'active' : '' }}" id="nav-special">
                <i data-lucide="calendar-range"></i>
                <span>Jadwal Khusus / Ujian</span>
            </a>
            <a href="{{ route('sounds.index') }}" class="nav-item {{ request()->routeIs('sounds.*') ? 'active' : '' }}" id="nav-sounds">
                <i data-lucide="volume-2"></i>
                <span>Master Suara</span>
            </a>
            <a href="{{ route('logs.index') }}" class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}" id="nav-logs">
                <i data-lucide="history"></i>
                <span>Riwayat Bel</span>
            </a>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" id="nav-settings">
                <i data-lucide="settings"></i>
                <span>Pengaturan & PIN</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="status-pill">
                <div class="status-indicator">
                    <span class="pulse-dot" id="system-status-dot"></span>
                    <span id="system-status-text">Sistem Siaga</span>
                </div>
                <button type="button" class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.75rem;" onclick="testChimeSound()" title="Uji Speaker">
                    <i data-lucide="play" style="width: 14px; height: 14px;"></i> Uji
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="clock-display">
                <div class="clock-time" id="realtime-clock">--:--:--</div>
                <div class="clock-date" id="realtime-date">Memuat waktu...</div>
            </div>

            <div class="top-actions">
                <!-- Mode Hening Toggle Button -->
                <button type="button" class="btn {{ \App\Models\Setting::get('mute_all', false) ? 'btn-danger' : 'btn-secondary' }}" id="btn-toggle-mute" onclick="toggleMuteMode()">
                    <i data-lucide="{{ \App\Models\Setting::get('mute_all', false) ? 'volume-x' : 'volume-2' }}" id="mute-icon"></i>
                    <span id="mute-text">{{ \App\Models\Setting::get('mute_all', false) ? 'Mode Hening: AKTIF' : 'Mode Normal' }}</span>
                </button>

                <!-- Tombol Bel Darurat / Manual -->
                <button type="button" class="btn btn-primary" id="btn-open-manual-bell" onclick="openManualBellModal()">
                    <i data-lucide="bell"></i>
                    <span>Bunyikan Bel Manual</span>
                </button>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div style="margin: 24px 32px 0; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="check-circle-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div style="margin: 24px 32px 0; background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3); color: #fb7185; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="alert-triangle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Content -->
        <main class="content-area">
            @yield('content')
        </main>
    </div>

    <!-- Modal Bunyikan Bel Manual -->
    <div class="modal-overlay" id="modal-manual-bell">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: #fff;">Bunyikan Bel Manual / Darurat</h3>
                <button onclick="closeManualBellModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer;"><i data-lucide="x"></i></button>
            </div>
            
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 18px;">
                Pilih suara yang akan langsung diputar melalui pengeras suara sekolah saat ini juga.
            </p>

            <form id="form-manual-bell" onsubmit="submitManualBell(event)">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Pilih Suara Bel</label>
                    <select id="manual-sound-id" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;" required>
                        @foreach(\App\Models\Sound::orderBy('language')->orderBy('name')->get() as $s)
                            <option value="{{ $s->id }}">[{{ strtoupper($s->language) }}] {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;">Keterangan / Alasan (Opsional)</label>
                    <input type="text" id="manual-label" placeholder="Contoh: Bel Masuk Upacara / Darurat" style="width: 100%; padding: 12px 14px; background: #1f2937; border: 1px solid var(--border-color); border-radius: 10px; color: #fff; font-size: 0.9rem;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeManualBellModal()">Batal</button>
                    <button type="submit" class="btn btn-danger" id="btn-confirm-manual-bell">
                        <i data-lucide="volume-2"></i> Bunyikan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Audio Player Element (Single persistent engine) -->
    <audio id="master-audio-player" preload="auto"></audio>

    <script>
        lucide.createIcons();

        // Audio Engine State
        const audioPlayer = document.getElementById('master-audio-player');
        let lastPlayedTriggerTime = 0;
        let masterVolume = {{ \App\Models\Setting::get('master_volume', 1.0) }};

        // Realtime Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('realtime-clock').textContent = `${hours}:${minutes}:${seconds}`;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('realtime-date').textContent = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Audio Playback Function
        function playAudioUrl(url, label = 'Bel Berbunyi', soundName = '') {
            if (!url) return;
            audioPlayer.src = url;
            audioPlayer.volume = masterVolume;
            audioPlayer.play().then(() => {
                showBellToast(label, soundName);
                animateBellIcon();
            }).catch(err => {
                console.warn("Audio autoplay blocked by browser or failed:", err);
            });
        }

        function animateBellIcon() {
            const bell = document.getElementById('sidebar-bell-icon');
            if (bell) {
                bell.classList.add('bell-ring-active');
                setTimeout(() => bell.classList.remove('bell-ring-active'), 5000);
            }
        }

        function showBellToast(label, soundName) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast-bell';
            toast.innerHTML = `
                <i data-lucide="bell-ring" style="width: 28px; height: 28px; color: #a5b4fc;"></i>
                <div>
                    <div style="font-weight: 700; font-size: 0.95rem;">${label}</div>
                    <div style="font-size: 0.8rem; color: #c7d2fe;">${soundName || 'Sedang berbunyi...'}</div>
                </div>
            `;
            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Background Polling Engine (Every 3 seconds)
        async function pollStatus() {
            try {
                const res = await fetch('{{ route("api.status") }}');
                if (!res.ok) return;
                const data = await res.json();

                masterVolume = data.master_volume || 1.0;

                // Cek trigger bel terbaru
                if (data.latest_trigger && data.latest_trigger.timestamp > lastPlayedTriggerTime) {
                    // Hanya putar jika timestamp trigger tidak lebih lama dari 90 detik
                    const nowSec = Date.now() / 1000;
                    if ((nowSec - data.latest_trigger.timestamp) < 90) {
                        lastPlayedTriggerTime = data.latest_trigger.timestamp;
                        if (!data.is_muted) {
                            playAudioUrl(data.latest_trigger.sound_url, data.latest_trigger.label, data.latest_trigger.sound_name);
                        }
                    }
                }

                // Update UI jika ada hook onStatusUpdate di view spesifik
                if (window.onStatusUpdate) {
                    window.onStatusUpdate(data);
                }
            } catch (err) {
                console.error("Polling status error:", err);
            }
        }
        setInterval(pollStatus, 3000);

        // Uji Chime Cepat
        function testChimeSound() {
            playAudioUrl('{{ asset("audio/id/chime.wav") }}', 'Uji Coba Suara', 'Nada Chime Standar');
        }

        // Toggle Mute Mode
        async function toggleMuteMode() {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('{{ route("api.toggle-mute") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });
                const data = await res.json();
                
                const btn = document.getElementById('btn-toggle-mute');
                const icon = document.getElementById('mute-icon');
                const text = document.getElementById('mute-text');

                if (data.is_muted) {
                    btn.className = 'btn btn-danger';
                    text.textContent = 'Mode Hening: AKTIF';
                    icon.setAttribute('data-lucide', 'volume-x');
                } else {
                    btn.className = 'btn btn-secondary';
                    text.textContent = 'Mode Normal';
                    icon.setAttribute('data-lucide', 'volume-2');
                }
                lucide.createIcons();
            } catch (e) {
                console.error(e);
            }
        }

        // Modal Controls
        function openManualBellModal() {
            document.getElementById('modal-manual-bell').classList.add('active');
        }

        function closeManualBellModal() {
            document.getElementById('modal-manual-bell').classList.remove('active');
        }

        async function submitManualBell(e) {
            e.preventDefault();
            const soundId = document.getElementById('manual-sound-id').value;
            const label = document.getElementById('manual-label').value;
            const btn = document.getElementById('btn-confirm-manual-bell');

            btn.disabled = true;
            btn.innerHTML = 'Membunyikan...';

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('{{ route("api.trigger-manual") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ sound_id: soundId, label: label })
                });

                const data = await res.json();
                if (data.success) {
                    closeManualBellModal();
                    lastPlayedTriggerTime = data.payload.timestamp;
                    playAudioUrl(data.payload.sound_url, data.payload.label, data.payload.sound_name);
                }
            } catch (err) {
                alert('Gagal membunyikan bel manual!');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="volume-2"></i> Bunyikan Sekarang';
                lucide.createIcons();
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
