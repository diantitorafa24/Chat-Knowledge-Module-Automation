# CIKA — Chat Intelligence & Knowledge Assistant for Module Automation

Bot Telegram internal yang menjawab pertanyaan staf NOC/Teknisi/CS berdasarkan dokumen SOP (DOCX) yang diupload admin. Menggunakan RAG (Retrieval-Augmented Generation): dokumen dipotong jadi chunk, diubah jadi vector (embedding), disimpan di Qdrant, lalu saat ada pertanyaan dicari chunk paling relevan dan dirangkum jawabannya oleh Gemini.

## ✅ Lokasi Proyek (SUDAH DISELESAIKAN — 2026-10-10)

Proyek sebelumnya sempat dipindahkan dengan nama folder panjang (`CIKA — Chat Intelligence & Knowledge Assistant for Module Automation`). **Sudah di-resolve:** folder kini bernama `cika` (sederhana, tanpa spasi/karakter khusus).

**Path final yang berlaku sekarang:** `C:\xampp\htdocs\cika`

Yang sudah dikerjakan saat resolve:
- Folder di-copy ke `C:\xampp\htdocs\cika`, folder lama dihapus
- `php artisan config:clear`, `cache:clear`, `view:clear` dijalankan dari path baru
- `php artisan storage:link` dijalankan ulang → symlink `public/storage` mengarah ke path baru yang benar
- Database `cika` dibuat ulang di MySQL XAMPP dan semua migration dijalankan (`php artisan migrate`)
- Admin user seeder dijalankan → `admin@cika.test` / `admin12345`

> ⚠️ **Catatan:** Database hasil migration ini adalah **database segar (kosong)**. Data uji lama (modul MOD-001, dokumen uji, chat log) tidak ikut pindah karena tidak ada backup SQL. Upload ulang dokumen diperlukan setelah proyek berjalan normal.

---

## Arsitektur

```
Admin upload DOCX (Web Admin)
   -> Parser baca isi (Heading, paragraf, list, tabel)
   -> Chunking per Heading (maks 1000 karakter/chunk)
   -> Embedding tiap chunk via Gemini (768 dimensi)
   -> Simpan ke Qdrant (vector DB)
   -> Admin "Aktifkan" versi modul -> chunk jadi is_active=true di Qdrant

Telegram (grup percobaan)
   -> n8n (Telegram Trigger)
   -> POST /api/rag/ask (Laravel)
        -> embed pertanyaan -> cari di Qdrant (filter is_active)
        -> kalau skor >= 0.60: kirim context ke Gemini -> jawaban
        -> kalau skor < 0.60: "tidak ditemukan di modul"
        -> simpan ke tabel chat_logs
   -> n8n kirim balasan ke Telegram + tombol feedback (suka/tidak suka)
   -> klik tombol -> POST /api/feedback -> tabel feedback
```

## Stack Teknis

| Komponen | Pilihan |
|---|---|
| Web Admin | Laravel 12 + Blade + Bootstrap 5 |
| Database | MySQL (XAMPP) |
| Parser DOCX | PHP native (ZipArchive + DOM), tanpa library tambahan |
| Queue | Laravel Queue (driver `database`) |
| Vector DB | Qdrant 1.19 (binary Windows, tanpa Docker) |
| Embedding | Gemini `gemini-embedding-2`, 768 dimensi |
| AI Jawaban | Gemini `gemini-3.5-flash-lite` |
| Otomasi | n8n (hosted di VPS — sumopod.my.id) |
| Jembatan lokal->internet | Cloudflare Tunnel (`cloudflared`, mode quick tunnel, URL sementara) |
| Bot Telegram | `@cika_module_bot` |

## Lokasi Penting

- **Project root:** `C:\xampp\htdocs\cika`
- **Laravel app:** `C:\xampp\htdocs\cika\web-admin`
- **Qdrant binary:** `C:\xampp\htdocs\cika\qdrant\bin\qdrant.exe`
- **File kredensial** (JANGAN commit, JANGAN screenshot): `C:\xampp\htdocs\cika\kredensial-cika.txt`
  > ⚠️ File ini ada di dalam `htdocs` — pastikan tidak bisa diakses via browser. Idealnya dipindah ke luar htdocs, misal `C:\kredensial-cika.txt`.
- **Env Laravel:** `C:\xampp\htdocs\cika\web-admin\.env` (berisi GEMINI_API_KEY, API_TOKEN, QDRANT_URL, dll)

**Cek keamanan wajib:** Buka `http://localhost/cika/web-admin/.env` di browser — harus 403/404, bukan isi file. Juga cek `http://localhost/cika/kredensial-cika.txt` — sama, harus tidak bisa diakses.

## Cara Menyalakan (Development, 3-4 terminal)

```powershell
# Terminal 1 — Qdrant
cd "C:\xampp\htdocs\cika\qdrant"
$env:QDRANT__SERVICE__HOST = "127.0.0.1"
.\bin\qdrant.exe

# Terminal 2 — Server web Laravel
cd "C:\xampp\htdocs\cika\web-admin"
php artisan serve

# Terminal 3 — Cloudflare Tunnel (buat n8n di VPS bisa akses API lokal)
cloudflared tunnel --url http://127.0.0.1:8000
# URL berubah setiap kali dijalankan ulang (quick tunnel gratis).
# Setelah dapat URL baru, update manual di n8n:
#   - node "Tanya CIKA" -> URL
#   - node "HTTP Request" (feedback) -> URL

# Terminal 4 (opsional, kalau upload dokumen baru) — Queue worker
cd "C:\xampp\htdocs\cika\web-admin"
php artisan queue:listen --tries=1 --timeout=1200
```

Web Admin: http://127.0.0.1:8000 (login: `admin@cika.test` / `admin12345` — wajib diganti sebelum produksi)

## Status per Phase

| Phase | Nama | Status |
|---|---|---|
| 1 | Requirement | Selesai |
| 2 | Laravel + Web Admin | Selesai |
| 3 | MySQL Database | Selesai |
| 4 | Upload DOCX | Selesai |
| 5 | DOCX Processor | Selesai |
| 6 | Chunking | Selesai |
| 7 | Embedding | Selesai |
| 8 | Vector Database (Qdrant) | Selesai |
| 9 | RAG API | Selesai |
| 10 | AI Response | Selesai |
| 11 | n8n | Selesai |
| 12 | Telegram @CIKA | Selesai |
| 13 | Chat Logs | Selesai |
| 14 | Monitoring (Dashboard) | Selesai |
| 15 | Versioning + Feedback | Hampir selesai — ada bug aktif, lihat di bawah |
| 16 | Security + Deployment | Belum dimulai |

## Bug Aktif yang Belum Selesai (Phase 15)

**Gejala:** Tombol feedback (suka/tidak suka) di Telegram selalu gagal dengan pesan API `"chat_log_id tidak valid."`

**Root cause yang sudah dikonfirmasi lewat log (`storage/logs/laravel.log`):**
Node n8n **"Tanya CIKA"** (HTTP Request ke `/api/rag/ask`) kadang mengirim body JSON dengan expression Telegram (`{{ $json.message.from.id }}`, dll) sebagai teks literal mentah, bukan hasil evaluasinya. Ini menyebabkan:

1. `chat_logs.telegram_user_id` gagal tersimpan (SQL error "Incorrect integer value") -> baris chat_log tidak tersimpan sama sekali untuk beberapa request.
2. Karena chat_log tidak tersimpan, tombol feedback yang dibuat berdasarkan `chat_log_id` dari respons API jadi kosong (`callback_data: fb_1_` tanpa angka di belakang).
3. Sudah dicoba perbaikan: ganti Body node dari mode "JSON" ke "Using Fields Below" di beberapa node — tapi node **"Send a text message"** (yang membuat tombol inline keyboard) belum diperiksa/diperbaiki — kemungkinan besar field **Callback Data** di tombol itu juga masih berupa teks literal `fb_1_{{ $json.chat_log_id }}`, bukan expression yang dievaluasi.

**Mitigasi sementara yang sudah diterapkan di Laravel** (`app/Http/Controllers/Api/FeedbackController.php`):
Kalau `chat_log_id` dari `callback_data` kosong, sistem mencoba fallback mengambil chat_log terakhir milik `telegram_user_id` yang sama. Mitigasi ini belum terbukti berhasil di test terakhir — masih error sama.

**Yang belum dicek:**
- Isi kolom "Callback Data" pada kedua tombol feedback di node **"Send a text message"** (yang mengirim jawaban pertama ke Telegram) — ini kemungkinan besar sumber masalah sebenarnya.
- Kemungkinan field itu perlu diubah ke mode Expression (klik ikon fx di n8n) baru diisi `fb_1_{{ $json.chat_log_id }}`.

## Belum Dikerjakan Sama Sekali

- **Phase 16 — Security + Deployment**: deploy Web Admin + API + Qdrant ke server permanen (idealnya VPS yang sama dengan n8n, supaya tidak butuh Cloudflare Tunnel lagi). Termasuk: ganti password admin default, HTTPS asli, backup database, rate limiting tambahan, environment production.
- Halaman **Users** (kelola admin lain) dan **Settings** di Web Admin — masih placeholder kosong di sidebar.
- Generate ulang `API_TOKEN` dan `TELEGRAM_BOT_TOKEN` sekali lagi untuk produksi (keduanya sempat ter-screenshot berkali-kali selama development — wajib diganti sebelum dipakai staf sungguhan).

## Tugas untuk Lanjutan Sesi Ini (urutan prioritas)

1. **Pastikan proyek jalan normal dari lokasi baru di htdocs** (lihat peringatan paling atas README ini). Ini WAJIB diselesaikan dulu sebelum lanjut apa pun, karena semua langkah lain mengasumsikan proyek bisa dijalankan.

2. **Selesaikan bug feedback Phase 15**:
   - Buka workflow n8n "CIKA - Test RAG Lengkap" (project di sumopod.my.id)
   - Periksa node "Send a text message" (yang kirim jawaban dengan tombol inline keyboard)
   - Pastikan field Callback Data pada kedua tombol memakai mode Expression dan terisi `fb_1_{{ $json.chat_log_id }}` / `fb_0_{{ $json.chat_log_id }}` yang benar-benar dievaluasi (bukan teks mentah)
   - Uji ulang: kirim pesan baru di Telegram grup percobaan -> klik tombol -> cek `SELECT * FROM feedback ORDER BY id DESC LIMIT 3;` di MySQL
   - Setelah berhasil: commit ke git dengan pesan yang jelas

3. **Audit semua node n8n lain** yang masih mungkin punya bug serupa (expression tidak dievaluasi) — terutama node "Tanya CIKA", pastikan body-nya pakai "Using Fields Below" dengan expression yang benar, bukan JSON mentah dengan `{{ }}` sebagai teks.

4. **Ganti ulang `API_TOKEN` dan `TELEGRAM_BOT_TOKEN`** (generate baru, update di `.env` dan n8n) karena sudah berkali-kali ter-expose di screenshot sepanjang sesi development sebelumnya.

5. **Mulai Phase 16**: rencanakan deployment Web Admin + Laravel API + Qdrant ke VPS yang sama dengan n8n (hindari Cloudflare Tunnel permanen untuk produksi), setup domain/subdomain tetap, HTTPS asli, ganti kredensial default.

6. Baca `docs/keputusan-teknis.md` dan `docs/README_CIKA_V2.md` (requirement asli proyek) untuk konteks lengkap sebelum mengubah struktur besar apa pun.

## Catatan Keamanan

- File kredensial (lihat "Lokasi Penting" di atas) berisi semua token/API key — jangan pernah di-screenshot, commit, atau tempel ke chat/dokumen mana pun.
- `.env` di `web-admin/` juga tidak boleh commit (sudah di `.gitignore`) — dan sekarang WAJIB dicek tidak bisa diakses via browser karena project ada di dalam htdocs (lihat peringatan keamanan di bagian "Lokasi Penting").
- Database `cika` di MySQL lokal berisi data uji (modul MOD-001, 3 dokumen uji, beberapa chat log) — aman dihapus/reset kalau development selanjutnya butuh data bersih.
#   C h a t - K n o w l e d g e - M o d u l e - A u t o m a t i o n  
 