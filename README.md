# Blueprint Pengembangan Aplikasi Bel Sekolah

**Versi dokumen:** 1.0
**Tanggal:** 6 September 2026
**Status:** Draft awal — patokan perancangan, development, dan testing

---

## 1. Ringkasan Proyek

Aplikasi Bel Sekolah adalah aplikasi yang berfungsi sebagai pengganti bel manual/lonceng sekolah. Aplikasi akan memutar alarm/suara pengingat secara otomatis pada jam-jam tertentu sesuai jadwal yang diset oleh admin, dengan dukungan dua bahasa (Indonesia & English) untuk suara notifikasinya.

### 1.1 Tujuan
- Mengotomatiskan bunyi bel sekolah sesuai jadwal harian.
- Memberi fleksibilitas pengaturan hari libur dan tanggal khusus (event, ujian, dll).
- Menyediakan pilihan bahasa suara alarm (ID/EN).

### 1.2 Target Pengguna
- Admin/Tata Usaha sekolah (pengelola jadwal).
- Operator harian (guru piket) — opsional, akses terbatas.

---

## 2. Fitur Utama (Functional Requirements)

| No | Fitur | Deskripsi |
|----|-------|-----------|
| F1 | Set Jam Alarm | Admin dapat menambah/edit/hapus jadwal jam bel (misal: 07:00 masuk, 09:30 istirahat, dst) |
| F2 | Set Hari Libur | Admin dapat menandai hari tertentu (mingguan/tanggal) sebagai libur, sehingga alarm tidak berbunyi |
| F3 | Set Tanggal Khusus untuk Jam Tertentu | Override jadwal normal pada tanggal spesifik (misal jadwal ujian, jadwal Ramadan, hari pertama sekolah) |
| F4 | Pilihan Bahasa Suara | Setiap alarm dapat diset suara pengumuman berbahasa Indonesia atau English |
| F5 | Preview/Test Suara | Admin bisa mendengarkan preview suara sebelum disimpan |
| F6 | Riwayat/Log Bel | Mencatat histori bel yang telah berbunyi (opsional, untuk audit) |
| F7 | Notifikasi/Volume Control | Pengaturan volume dan durasi bunyi alarm |

### 2.1 Fitur Tambahan (Nice to Have — bisa masuk fase 2)
- Multi-profil jadwal (jadwal reguler vs jadwal bulan puasa).
- Backup/restore konfigurasi jadwal (export/import JSON).
- Mode "silent" sementara (misal saat ada acara khusus).
- Integrasi dengan speaker eksternal via Bluetooth.

---

## 3. Non-Functional Requirements

| Aspek | Ketentuan |
|-------|-----------|
| Reliabilitas | Alarm harus tetap berbunyi tepat waktu meski aplikasi berjalan di background/HP dikunci |
| Akurasi Waktu | Toleransi keterlambatan bunyi maksimal ±2 detik dari waktu yang diset |
| Ketersediaan Offline | Aplikasi harus tetap berfungsi tanpa koneksi internet (semua data lokal) |
| Kompatibilitas | Android minimum versi 8.0 (API 26) ke atas / Desktop (Windows) jika dibutuhkan versi PC |
| Keamanan | Pengaturan jadwal dilindungi PIN/password agar tidak diubah sembarangan |
| Daya Tahan Baterai | Konsumsi baterai minimal saat idle di background |

---

## 4. Pemilihan Platform & Tech Stack

> Catatan: pilih salah satu sesuai kebutuhan sekolah (device yang tersedia — HP Android, PC/laptop yang selalu nyala, atau keduanya).

### Opsi A — Laravel + NativePHP Desktop (★ Direkomendasikan — sesuai keahlian developer)
Membungkus aplikasi Laravel menjadi aplikasi desktop native (Electron di baliknya), dibundel jadi satu file installer sehingga client tidak perlu install PHP/Composer/web server secara manual.

- **Framework:** Laravel 12.x + `nativephp/desktop` (^2.0)
- **Frontend:** Blade + Livewire/Flux (opsional Vue/React via Vite jika ingin UI lebih interaktif)
- **Database lokal:** SQLite — otomatis dikonfigurasi oleh NativePHP, file database dibuat di direktori appdata milik user, tanpa setup manual
- **Scheduler:** Laravel Task Scheduling (`php artisan schedule:run`) dijalankan via background queue worker bawaan NativePHP (`QUEUE_CONNECTION=database`), terpisah dari main thread sehingga tidak mengganggu UI
- **Audio:** HTML5 `<audio>` di window Electron (Chromium), dipicu dari job/queue saat waktu alarm tercapai
- **Notifikasi native:** `Dialog::toast()` / native notification untuk indikator visual saat bel berbunyi
- **Auto-start:** diaktifkan agar aplikasi otomatis jalan saat PC dinyalakan/restart (penting jika listrik padam)
- **System tray:** app tetap "hidup" di background meski window di-minimize — tidak perlu ditutup total
- **Packaging/Installer:** `php artisan native:build` menghasilkan installer `.exe` (Windows), `.dmg` (Mac), `.AppImage` (Linux)

**Catatan penting:** karena berbasis Electron, aplikasi harus tetap berjalan (boleh di-tray, tidak boleh di-force-close/quit) agar scheduler & audio tetap aktif. Ini beda dengan aplikasi Android native yang bisa memicu alarm meski app tertutup total.

### Opsi B — Aplikasi Android (alternatif jika device utama adalah HP, bukan PC)
- **Bahasa:** Kotlin
- **Scheduler:** `AlarmManager` (exact alarm) + `WorkManager` (fallback/redundansi)
- **Audio:** `MediaPlayer` / `ExoPlayer`
- **Database lokal:** Room (SQLite)
- **Background service:** Foreground Service agar tidak dimatikan sistem (Doze Mode)
- **Kelebihan vs Opsi A:** alarm tetap terpicu meski app force-close (OS-level alarm, bukan bergantung app tetap berjalan)

### Opsi C — Web App biasa (PWA, dibuka di browser tab yang selalu aktif)
- **Frontend:** React/Vue, backend tetap bisa Laravel (API only)
- **Scheduler:** setInterval + validasi waktu server-side (butuh tab tetap terbuka)
- **Kekurangan:** kurang reliable dibanding native app untuk alarm presisi, dan tidak ada installer — instalasi via browser saja

**Rekomendasi:** Opsi A (Laravel + NativePHP) — paling sesuai dengan keahlian developer, proses development memakai tools Laravel sepenuhnya (Eloquent, Artisan, migration, dsb), sekaligus tetap menghasilkan installer native yang mudah dipasang client, dengan SQLite yang memang cocok untuk skala data kecil seperti ini.

---

## 5. Perancangan Data (Data Model)

### 5.1 Tabel `schedule` (Jadwal Reguler)
| Field | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER (PK) | ID unik |
| time | TEXT (HH:mm) | Jam bunyi alarm |
| label | TEXT | Nama event, misal "Masuk Kelas", "Istirahat" |
| days_of_week | TEXT (JSON array) | Hari berlaku, misal `["Mon","Tue","Wed","Thu","Fri"]` |
| sound_id | INTEGER (FK) | Relasi ke tabel `sound` |
| language | TEXT | `id` / `en` |
| is_active | BOOLEAN | Aktif/nonaktif tanpa harus hapus |

### 5.2 Tabel `holiday` (Hari Libur)
| Field | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER (PK) | ID unik |
| date | DATE | Tanggal libur (untuk libur nasional/spesifik) |
| is_recurring_weekly | BOOLEAN | True jika libur mingguan (misal semua hari Minggu) |
| day_of_week | TEXT | Diisi jika recurring weekly |
| description | TEXT | Keterangan, misal "Libur Idul Fitri" |

### 5.3 Tabel `special_schedule` (Jadwal Tanggal Khusus / Override)
| Field | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER (PK) | ID unik |
| date | DATE | Tanggal khusus berlaku |
| time | TEXT (HH:mm) | Jam alarm khusus |
| label | TEXT | Nama event, misal "Jadwal Ujian" |
| sound_id | INTEGER (FK) | Suara yang dipakai |
| language | TEXT | `id` / `en` |
| overrides_regular | BOOLEAN | True = jadwal reguler di tanggal ini diabaikan |

### 5.4 Tabel `sound` (Master Suara)
| Field | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER (PK) | ID unik |
| name | TEXT | Nama suara, misal "Bel Masuk - ID" |
| file_path | TEXT | Lokasi file audio |
| language | TEXT | `id` / `en` |
| duration_sec | INTEGER | Durasi file |

### 5.5 Logika Prioritas Eksekusi Harian
```
Untuk setiap hari:
1. Cek apakah tanggal hari ini ada di tabel `holiday` → jika ya, SKIP semua alarm reguler hari itu
   (kecuali ada override di special_schedule).
2. Cek apakah tanggal hari ini ada di tabel `special_schedule` → jika ya, gunakan jadwal ini,
   dan jika overrides_regular = true, abaikan jadwal reguler.
3. Jika tidak ada holiday & tidak ada special_schedule, jalankan `schedule` reguler
   sesuai hari (days_of_week) yang cocok dengan hari ini.
```

---

## 6. Perancangan UI/UX (Wireframe Level)

### 6.1 Struktur Halaman
1. **Home / Dashboard** — menampilkan jadwal hari ini, status "libur/aktif", alarm berikutnya.
2. **Kelola Jadwal Alarm** — list + tambah/edit/hapus jam alarm reguler.
3. **Kelola Hari Libur** — kalender untuk menandai tanggal libur / hari mingguan libur.
4. **Kelola Tanggal Khusus** — form tambah override untuk tanggal tertentu.
5. **Pengaturan Suara** — pilih bahasa default, upload/pilih file suara, preview/test.
6. **Pengaturan Umum** — volume, PIN keamanan, backup/restore data.

### 6.2 Alur Utama (User Flow)
```
Buka App → Dashboard → [Tambah Jadwal] → Isi jam, hari, pilih suara+bahasa → Simpan
                     → [Tandai Libur] → Pilih tanggal/hari → Simpan
                     → [Tanggal Khusus] → Pilih tanggal, jam, suara → Simpan
```

---

## 7. Tahapan Pengembangan (Development Roadmap)

### Fase 0 — Perencanaan (1 minggu)
- [ ] Finalisasi platform (Android/Desktop/Web)
- [ ] Finalisasi daftar suara bel yang dibutuhkan (ID & EN)
- [ ] Approval wireframe/UI oleh pihak sekolah
- [ ] Setup repository & environment development

### Fase 1 — Setup Dasar & Database (1 minggu)
- [ ] Setup project & struktur folder
- [ ] Implementasi database lokal (schema sesuai section 5)
- [ ] Seed data suara default (ID & EN)

### Fase 2 — Core Feature: Jadwal Alarm (2 minggu)
- [ ] CRUD jadwal alarm reguler (F1)
- [ ] Scheduler/engine yang mengecek waktu & memicu alarm
- [ ] Pemutaran suara sesuai bahasa yang diset (F4)
- [ ] Preview suara (F5)

### Fase 3 — Hari Libur & Tanggal Khusus (1–2 minggu)
- [ ] CRUD hari libur (F2)
- [ ] CRUD tanggal khusus/override (F3)
- [ ] Implementasi logika prioritas (section 5.5)

### Fase 4 — Reliabilitas Background (1 minggu)
- [ ] Foreground service / background scheduler (Android)
- [ ] Handle restart HP (BOOT_COMPLETED) agar jadwal tetap jalan
- [ ] Battery optimization whitelist / exclude dari Doze Mode

### Fase 5 — Pengaturan Tambahan (1 minggu)
- [ ] Volume & durasi bel (F7)
- [ ] PIN/password proteksi pengaturan
- [ ] Log riwayat bel (F6, opsional)

### Fase 6 — Testing (lihat section 8) (1–2 minggu)

### Fase 7 — Deployment & Serah Terima (3–5 hari)
- [ ] Build APK/installer final
- [ ] Instalasi di device sekolah
- [ ] Training singkat untuk admin/TU
- [ ] Dokumentasi user manual

---

## 8. Rencana Testing

### 8.1 Unit Testing
- Test fungsi pengecekan hari libur vs hari aktif.
- Test fungsi resolusi prioritas (holiday vs special_schedule vs regular).
- Test parsing waktu (format HH:mm, edge case 23:59 → 00:00).

### 8.2 Integration Testing
- Test alarm benar-benar terpicu sesuai jadwal yang disimpan di database.
- Test perpindahan bahasa suara (ID ↔ EN) langsung berubah tanpa restart app.
- Test override tanggal khusus benar-benar mengalahkan jadwal reguler.

### 8.3 Testing Reliabilitas (khusus Android)
| Skenario | Ekspektasi |
|----------|-----------|
| HP di-restart | Jadwal tetap aktif setelah boot |
| App di-force close | Alarm tetap berbunyi (via AlarmManager, bukan bergantung app terbuka) |
| Mode hemat baterai aktif | Alarm tetap presisi (perlu battery optimization exception) |
| Layar terkunci | Suara tetap terdengar |
| Tidak ada koneksi internet | Semua fitur tetap berjalan normal |

### 8.4 User Acceptance Testing (UAT)
- Admin sekolah mencoba set jadwal 1 minggu penuh (termasuk 1 hari libur & 1 tanggal khusus) dan memverifikasi bunyi bel sesuai ekspektasi secara langsung di lokasi.
- Uji coba minimal 3–5 hari berjalan riil sebelum go-live penuh.

### 8.5 Kriteria Lulus (Exit Criteria)
- [ ] Semua alarm berbunyi tepat waktu (toleransi ±2 detik) selama 5 hari uji coba berturut-turut.
- [ ] Tidak ada bug kritikal (alarm tidak bunyi / bunyi di waktu salah).
- [ ] Admin sekolah bisa mengoperasikan semua fitur tanpa bantuan developer.

---

## 9. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Android membatasi background process (Doze Mode) sehingga alarm telat/tidak bunyi | Gunakan `setExactAndAllowWhileIdle()` pada AlarmManager + minta user exclude app dari battery optimization |
| HP admin mati/restart tanpa sepengetahuan | Tambahkan BroadcastReceiver `BOOT_COMPLETED` untuk re-schedule otomatis |
| Kesalahan input jadwal oleh admin | Validasi input & konfirmasi sebelum simpan, tambahkan PIN untuk mencegah perubahan tidak sengaja |
| File suara hilang/corrupt | Simpan suara default sebagai fallback, validasi file saat load |

---

## 10. Lampiran — Contoh Daftar Suara Default

| Nama Event | Bahasa Indonesia | English |
|------------|-------------------|---------|
| Masuk Kelas | "Bel masuk kelas, para siswa harap segera menuju kelas." | "School bell, please proceed to your classroom." |
| Istirahat | "Bel istirahat telah berbunyi." | "Recess bell has rung." |
| Pulang Sekolah | "Bel pulang sekolah, sampai jumpa besok." | "School is over, see you tomorrow." |

---

*Dokumen ini adalah acuan awal dan bersifat living document — dapat direvisi seiring diskusi lebih lanjut dengan pihak sekolah maupun temuan selama development.*
