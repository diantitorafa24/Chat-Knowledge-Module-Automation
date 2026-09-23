# CIKA — Chat Intelligence & Knowledge Assistant for Module Automation

CIKA adalah sistem **AI Knowledge Assistant** yang digunakan melalui Telegram untuk membantu pengguna memahami dan menjalankan pekerjaan berdasarkan modul kerja yang disediakan oleh perusahaan.

Berbeda dengan chatbot biasa yang knowledge-nya ditulis langsung di workflow, CIKA menggunakan **file DOCX sebagai sumber modul utama**. Administrator dapat mengunggah file DOCX melalui Web Admin. Sistem kemudian memproses isi dokumen secara otomatis agar dapat digunakan oleh AI ketika menjawab pertanyaan pengguna.

Pengguna dapat memanggil CIKA di grup Telegram dengan melakukan mention:

```text
@CIKA pertanyaan yang ingin ditanyakan
```

CIKA akan mencari informasi yang relevan dari modul yang telah diproses, kemudian AI menyusun jawaban berdasarkan informasi tersebut.

Sistem CIKA terdiri dari beberapa komponen utama:

```text
1. Web Admin
2. MySQL Database
3. File Storage
4. Document Processor
5. Vector Database
6. n8n
7. AI Model
8. Telegram Bot
```

---

# 1. Tujuan

Tujuan utama pembuatan CIKA adalah membuat sistem assistant internal yang dapat membantu pengguna memahami prosedur atau cara menjalankan pekerjaan berdasarkan modul yang telah disediakan.

CIKA diharapkan dapat:

- Membantu pengguna memahami prosedur pekerjaan.
- Membaca dan memproses modul dalam bentuk DOCX.
- Menyimpan metadata dan informasi modul.
- Mengubah isi dokumen menjadi knowledge yang dapat dicari AI.
- Menjawab pertanyaan berdasarkan isi modul.
- Memudahkan admin menambahkan modul baru.
- Memudahkan admin mengganti atau memperbarui modul.
- Memudahkan admin mengaktifkan atau menonaktifkan modul.
- Menyimpan riwayat penggunaan CIKA.
- Mengurangi pertanyaan berulang mengenai prosedur pekerjaan.
- Membantu standarisasi informasi dan prosedur internal.

---

# 2. Konsep Utama

Konsep utama CIKA adalah:

```text
Admin
  │
  ▼
Web Admin
  │
  │ Upload DOCX
  ▼
File Storage
  │
  ▼
Document Processing
  │
  ├── Extract Text
  ├── Clean Text
  ├── Chunking
  └── Metadata
  │
  ▼
Embedding
  │
  ▼
Vector Database
  │
  ▼
n8n
  │
  ▼
AI Model
  │
  ▼
Telegram
  │
  ▼
User
```

Dengan konsep tersebut, admin **tidak perlu mengetik knowledge secara manual**.

Admin cukup menyediakan file DOCX sebagai modul.

Contoh:

```text
Troubleshooting NOC.docx
MikroTik.docx
Fiber Optic.docx
CCTV.docx
Customer Service.docx
```

Kemudian sistem memproses file tersebut secara otomatis.

---

# 3. Arsitektur Sistem

Arsitektur keseluruhan CIKA:

```text
                         ┌──────────────────┐
                         │      ADMIN       │
                         └────────┬─────────┘
                                  │
                                  ▼
                         ┌──────────────────┐
                         │    WEB ADMIN     │
                         │                  │
                         │ Upload DOCX      │
                         │ Manage Module    │
                         │ Manage Version   │
                         │ Manage Status    │
                         └────────┬─────────┘
                                  │
                    ┌─────────────┴─────────────┐
                    │                           │
                    ▼                           ▼
             ┌──────────────┐            ┌──────────────┐
             │    MySQL     │            │ File Storage │
             │              │            │    .DOCX     │
             └──────────────┘            └──────┬───────┘
                                                │
                                                ▼
                                      ┌──────────────────┐
                                      │ Document Parser  │
                                      │                  │
                                      │ Extract Text     │
                                      │ Clean Text       │
                                      │ Chunking         │
                                      └────────┬─────────┘
                                               │
                                               ▼
                                      ┌──────────────────┐
                                      │    Embedding     │
                                      └────────┬─────────┘
                                               │
                                               ▼
                                      ┌──────────────────┐
                                      │   Vector DB      │
                                      └────────┬─────────┘
                                               │
                                               ▼
                                      ┌──────────────────┐
                                      │       n8n        │
                                      │ Automation       │
                                      └────────┬─────────┘
                                               │
                                      ┌────────┴────────┐
                                      │                 │
                                      ▼                 ▼
                               ┌─────────────┐   ┌─────────────┐
                               │ Telegram Bot│   │  AI Model   │
                               └──────┬──────┘   └──────┬──────┘
                                      │                 │
                                      └────────┬────────┘
                                               ▼
                                      ┌──────────────────┐
                                      │ Telegram Group  │
                                      │      @CIKA       │
                                      └──────────────────┘
```

---

# 4. Cara Kerja CIKA

## 4.1 Admin Upload Modul

Admin membuka Web Admin:

```text
Dashboard
   ↓
Modules
   ↓
Upload Module
```

Admin memilih file:

```text
Troubleshooting NOC.docx
```

Kemudian sistem menerima file tersebut.

---

## 4.2 Sistem Memproses DOCX

File DOCX diproses menjadi teks.

```text
DOCX
 ↓
Extract Text
 ↓
Clean Text
 ↓
Preserve Structure
 ↓
Chunking
```

Contoh isi DOCX:

```text
PROSEDUR TROUBLESHOOTING PELANGGAN

1. Periksa ID pelanggan.
2. Periksa status koneksi.
3. Periksa log MikroTik.
4. Lakukan pengecekan perangkat.
5. Lakukan eskalasi kepada teknisi.
```

Dapat diproses menjadi beberapa bagian:

```text
Chunk 1:
PROSEDUR TROUBLESHOOTING PELANGGAN

Chunk 2:
1. Periksa ID pelanggan.
2. Periksa status koneksi.

Chunk 3:
3. Periksa log MikroTik.

Chunk 4:
4. Lakukan pengecekan perangkat.

Chunk 5:
5. Lakukan eskalasi kepada teknisi.
```

---

# 5. Chunking

Chunking adalah proses memecah dokumen menjadi bagian-bagian yang lebih kecil.

Hal ini diperlukan karena dokumen dapat memiliki banyak halaman.

Contoh:

```text
DOCX
│
├── Chunk 1
├── Chunk 2
├── Chunk 3
├── Chunk 4
├── Chunk 5
└── ...
```

Setiap chunk dapat memiliki metadata:

```text
module_id
document_id
section
subsection
chunk_index
content
```

Contoh:

```text
Module:
Troubleshooting NOC

Section:
Troubleshooting

Subsection:
Pelanggan Tidak Terhubung

Content:
Periksa status koneksi pelanggan...
```

---

# 6. Embedding

Setelah dokumen dipecah menjadi chunk, setiap chunk dapat diproses menjadi embedding.

Konsep:

```text
Text
 ↓
Embedding Model
 ↓
Vector
```

Contoh sederhana:

```text
"Periksa status koneksi pelanggan"
             ↓
       Embedding Model
             ↓
[0.021, -0.143, 0.552, ...]
```

Embedding digunakan untuk mencari informasi yang memiliki makna atau konteks yang relevan dengan pertanyaan pengguna.

---

# 7. Vector Database

Embedding disimpan di Vector Database.

Struktur:

```text
Document
   │
   ▼
Chunks
   │
   ▼
Embeddings
   │
   ▼
Vector Database
```

Ketika pengguna bertanya, sistem dapat melakukan semantic search untuk menemukan chunk yang paling relevan.

---

# 8. Retrieval-Augmented Generation (RAG)

CIKA menggunakan konsep RAG untuk menghubungkan pertanyaan pengguna dengan isi modul.

RAG terdiri dari:

```text
Retrieval
+
Augmentation
+
Generation
```

Alur:

```text
User Question
      │
      ▼
Search Vector Database
      │
      ▼
Relevant Chunks
      │
      ▼
Context
      │
      ▼
AI Model
      │
      ▼
Answer
```

Dengan cara ini, AI mendapatkan context dari modul sebelum membuat jawaban.

---

# 9. Telegram AI Chatbot

CIKA digunakan melalui grup Telegram.

CIKA hanya merespons apabila pengguna melakukan mention.

Contoh:

```text
@CIKA bagaimana cara mengecek pelanggan
yang jaringan internetnya mati?
```

Pesan tersebut diterima oleh Telegram Bot dan diteruskan ke workflow n8n.

---

# 10. Workflow n8n

Workflow utama:

```text
Telegram Trigger
       │
       ▼
Check Mention @CIKA
       │
       ▼
Extract Question
       │
       ▼
Generate Search Query
       │
       ▼
Vector Search
       │
       ▼
Get Relevant Chunks
       │
       ▼
Prepare AI Context
       │
       ▼
AI Model
       │
       ▼
Generate Answer
       │
       ├──────────────► Save Chat Log
       │
       ▼
Telegram Send Message
```

---

# 11. Step-by-Step Workflow n8n

## Step 1 — Telegram Trigger

n8n menerima pesan dari grup Telegram.

Contoh:

```text
@CIKA bagaimana cara melakukan pengecekan
pelanggan yang jaringan internetnya mati?
```

---

## Step 2 — Check Mention

Workflow memeriksa apakah pesan mengandung mention CIKA.

Jika tidak:

```text
STOP
```

Jika terdapat:

```text
@CIKA
```

workflow dilanjutkan.

---

## Step 3 — Extract Question

Mention CIKA dihapus.

Dari:

```text
@CIKA bagaimana cara mengecek pelanggan mati?
```

menjadi:

```text
bagaimana cara mengecek pelanggan mati?
```

---

## Step 4 — Vector Search

Pertanyaan digunakan untuk mencari chunk yang relevan.

Contoh:

```text
Question:
bagaimana cara mengecek pelanggan mati?
```

Vector search menemukan:

```text
Chunk 17
Chunk 23
Chunk 24
```

---

## Step 5 — Prepare Context

Chunk yang ditemukan disusun menjadi context untuk AI.

Contoh:

```text
MODULE:
Troubleshooting NOC

RELEVANT CONTENT:

1. Periksa ID pelanggan.
2. Periksa status koneksi.
3. Periksa log MikroTik.
4. Periksa perangkat pelanggan.
5. Lakukan eskalasi jika diperlukan.
```

---

## Step 6 — AI Model

AI menerima:

```text
User Question
+
Relevant Module
+
Relevant Chunks
+
System Instructions
```

AI kemudian menyusun jawaban.

---

# 12. AI System Prompt

Contoh system prompt:

```text
Kamu adalah CIKA
(Chat Intelligence & Knowledge Assistant for Module Automation).

Tugas kamu adalah membantu pengguna memahami dan
menjalankan pekerjaan berdasarkan modul yang diberikan
oleh sistem.

Gunakan context dari module sebagai sumber utama jawaban.

Jangan membuat prosedur baru yang tidak terdapat pada
context.

Jika informasi yang dibutuhkan tidak tersedia dalam
context, jangan mengarang jawaban.

Jika informasi tidak ditemukan, jawab:

"Maaf, CIKA belum menemukan panduan yang sesuai
dengan pertanyaan tersebut. Silakan hubungi admin
atau supervisor terkait."

Ketentuan jawaban:

- Gunakan Bahasa Indonesia.
- Jawaban harus jelas dan mudah dipahami.
- Gunakan langkah bernomor apabila informasi berupa prosedur.
- Jangan mengubah maksud dari prosedur pada module.
- Jangan menambahkan prosedur yang tidak terdapat dalam module.
- Jika terdapat beberapa prosedur, pilih yang paling relevan.
```

---

# 13. Contoh Pertanyaan

User:

```text
@CIKA gimana cara cek pelanggan yang jaringan mati?
```

Sistem:

```text
Telegram
   ↓
n8n
   ↓
Extract Question
   ↓
Vector Search
   ↓
Relevant Chunks
   ↓
AI
```

Context yang ditemukan:

```text
PROSEDUR TROUBLESHOOTING PELANGGAN

1. Periksa ID pelanggan.
2. Periksa status koneksi.
3. Periksa log MikroTik.
4. Lakukan pengecekan perangkat.
5. Lakukan eskalasi kepada teknisi.
```

Output:

```text
CIKA:

Untuk melakukan pengecekan pelanggan yang mengalami
gangguan jaringan:

1. Periksa ID pelanggan.
2. Periksa status koneksi.
3. Periksa log MikroTik.
4. Lakukan pengecekan perangkat.
5. Jika masalah belum terselesaikan, lakukan eskalasi
   kepada teknisi.
```

---

# 14. Jika Knowledge Tidak Ditemukan

Apabila CIKA tidak menemukan informasi yang relevan:

```text
CIKA:

Maaf, CIKA belum menemukan panduan yang sesuai
dengan pertanyaan tersebut.

Silakan hubungi admin atau supervisor terkait
untuk mendapatkan panduan yang sesuai.
```

CIKA tidak diperbolehkan mengarang prosedur internal.

---

# 15. Web Admin

Web Admin merupakan pusat pengelolaan module.

Admin dapat:

- Upload module DOCX.
- Melihat module.
- Mengubah metadata module.
- Mengganti file module.
- Mengaktifkan module.
- Menonaktifkan module.
- Menghapus module.
- Melakukan re-processing.
- Melihat versi module.
- Melihat status processing.
- Melihat riwayat module.

---

# 16. Menu Web Admin

Struktur menu:

```text
Dashboard

Modules
├── All Modules
├── Upload Module
├── Categories
└── Versions

Knowledge
├── Documents
├── Processing
└── Chunks

Monitoring
├── Chat Logs
├── Unanswered Questions
└── Feedback

System
├── Users
└── Settings
```

---

# 17. Dashboard

Dashboard dapat menampilkan:

```text
┌──────────────────────────────┐
│ Total Module        25       │
├──────────────────────────────┤
│ Total Documents     25       │
├──────────────────────────────┤
│ Total Chunks        850      │
├──────────────────────────────┤
│ Pertanyaan Hari Ini 37       │
├──────────────────────────────┤
│ Module Processing   2        │
└──────────────────────────────┘
```

Informasi tambahan:

```text
Module terbaru
Pertanyaan terbaru
Pertanyaan tidak terjawab
Module yang sering digunakan
Status processing
```

---

# 18. Upload Module

Halaman upload:

```text
┌─────────────────────────────────────────┐
│             UPLOAD MODULE               │
├─────────────────────────────────────────┤
│                                         │
│ Nama Module                             │
│ [ Troubleshooting NOC                ] │
│                                         │
│ Kategori                                │
│ [ NOC ▼                              ] │
│                                         │
│ File Module                             │
│ [ Choose File ]  Troubleshooting.docx  │
│                                         │
│ Deskripsi                               │
│ [ Panduan troubleshooting NOC         ] │
│                                         │
│              [ UPLOAD MODULE ]          │
└─────────────────────────────────────────┘
```

File utama pada tahap awal:

```text
.docx
```

Format lain dapat ditambahkan pada pengembangan berikutnya.

---

# 19. Processing Status

Setelah upload, module tidak langsung dianggap siap.

Status dapat berupa:

```text
Uploaded
Processing
Extracting
Chunking
Embedding
Indexed
Active
Failed
```

Contoh:

```text
Troubleshooting NOC.docx

Status:
Embedding

Progress:
72%
```

Setelah selesai:

```text
Status:
Active
```

---

# 20. Module Management

Contoh halaman:

```text
--------------------------------------------------------------------
Code      Module                 File                 Status
--------------------------------------------------------------------
MOD-001   Troubleshooting NOC    NOC.docx             Active
MOD-002   MikroTik               MikroTik.docx        Active
MOD-003   Fiber Optic            FO.docx              Processing
--------------------------------------------------------------------
```

Action:

```text
View
Edit
Replace File
Re-process
Deactivate
Delete
```

---

# 21. Replace Module

Jika isi modul diperbarui, admin dapat mengganti file.

Contoh:

```text
Troubleshooting-NOC-v1.docx
```

diganti:

```text
Troubleshooting-NOC-v2.docx
```

Alur:

```text
Old Document
      │
      ▼
Deactivate
      │
      ▼
Upload New Document
      │
      ▼
Extract
      │
      ▼
Chunk
      │
      ▼
Embedding
      │
      ▼
Vector Database
      │
      ▼
Active
```

Dengan demikian CIKA menggunakan modul terbaru.

---

# 22. Module Versioning

Module dapat memiliki version.

Contoh:

```text
Troubleshooting NOC

v1.0
v1.1
v2.0
```

Tabel:

```text
module_versions
```

Contoh data:

```text
Module:
Troubleshooting NOC

Current Version:
v2.0

Previous Version:
v1.1

Previous Version:
v1.0
```

Versioning berguna untuk mengetahui perubahan module dan memungkinkan pengelolaan versi dokumen.

---

# 23. Database

Database utama menggunakan:

```text
MySQL
```

MySQL digunakan untuk menyimpan data aplikasi dan metadata.

Tabel utama:

```text
users
categories
modules
module_versions
documents
chat_logs
feedback
```

Data chunk dan embedding dapat disimpan menggunakan Vector Database sesuai arsitektur yang dipilih.

---

# 24. Struktur Database MySQL

## 24.1 users

```text
users
├── id
├── name
├── email
├── password
├── role
├── created_at
└── updated_at
```

---

## 24.2 categories

```text
categories
├── id
├── name
├── description
├── status
├── created_at
└── updated_at
```

Contoh:

```text
NOC
Teknisi
Customer Service
Administrasi
Network
```

---

## 24.3 modules

```text
modules
├── id
├── category_id
├── code
├── name
├── description
├── status
├── current_version
├── created_at
└── updated_at
```

---

## 24.4 module_versions

```text
module_versions
├── id
├── module_id
├── version
├── document_id
├── status
├── created_at
└── updated_at
```

---

## 24.5 documents

```text
documents
├── id
├── module_id
├── filename
├── original_filename
├── file_path
├── file_size
├── mime_type
├── processing_status
├── processed_at
├── created_at
└── updated_at
```

---

# 25. Document Chunks

Chunk merupakan hasil pemecahan isi dokumen.

Konsep:

```text
documents
     │
     ▼
document_chunks
```

Struktur:

```text
document_chunks
├── id
├── document_id
├── module_id
├── chunk_index
├── section
├── subsection
├── content
├── metadata
└── created_at
```

Embedding dapat disimpan pada Vector Database atau sistem vector storage yang digunakan.

---

# 26. Chat Logs

Digunakan untuk menyimpan interaksi pengguna dengan CIKA.

```text
chat_logs
├── id
├── telegram_user_id
├── username
├── group_id
├── question
├── answer
├── module_id
├── created_at
└── updated_at
```

Data ini dapat digunakan untuk:

- Monitoring.
- Analisis pertanyaan.
- Mengetahui pertanyaan yang sering muncul.
- Mengetahui module yang sering digunakan.
- Mengidentifikasi knowledge yang belum tersedia.

---

# 27. Feedback

Pengguna dapat memberikan feedback terhadap jawaban CIKA.

Contoh:

```text
Apakah jawaban ini membantu?

👍 Ya
👎 Tidak
```

Data:

```text
feedback
├── id
├── chat_log_id
├── telegram_user_id
├── rating
├── comment
└── created_at
```

---

# 28. Relasi Database

Konsep relasi:

```text
categories
     │
     │ 1:N
     ▼
modules
     │
     │ 1:N
     ▼
module_versions
     │
     │ 1:1 / 1:N
     ▼
documents
     │
     │ 1:N
     ▼
document_chunks
```

Sedangkan:

```text
modules
   │
   └──────────────► chat_logs
```

---

# 29. Vector Database

Vector Database digunakan untuk menyimpan representasi embedding dari document chunks.

Konsep:

```text
Document Chunk
      │
      ▼
Embedding Model
      │
      ▼
Vector
      │
      ▼
Vector Database
```

Ketika user bertanya:

```text
Question
   │
   ▼
Embedding
   │
   ▼
Similarity Search
   │
   ▼
Relevant Chunks
```

---

# 30. Web Admin → Processing

Alur ketika admin upload:

```text
Admin
 │
 ▼
Web Admin
 │
 ▼
Upload DOCX
 │
 ▼
Save File
 │
 ▼
Create Document Record
 │
 ▼
Document Processing
 │
 ├── Extract Text
 │
 ├── Clean Text
 │
 ├── Detect Structure
 │
 ├── Chunking
 │
 └── Embedding
 │
 ▼
Vector Database
 │
 ▼
Processing Completed
 │
 ▼
Module Active
```

---

# 31. API Web Admin → n8n

Web Admin dapat menyediakan API untuk komunikasi dengan n8n.

Contoh:

```text
GET /api/modules
GET /api/modules/{id}
GET /api/modules/{id}/documents
GET /api/modules/search
POST /api/chat-logs
POST /api/feedback
```

Untuk proses retrieval, n8n dapat menggunakan endpoint atau service vector search sesuai implementasi.

---

# 32. Struktur Project Laravel

```text
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php
│       ├── ModuleController.php
│       ├── CategoryController.php
│       ├── DocumentController.php
│       ├── VersionController.php
│       ├── ChatLogController.php
│       ├── FeedbackController.php
│       └── UserController.php
│
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Module.php
│   ├── ModuleVersion.php
│   ├── Document.php
│   ├── ChatLog.php
│   └── Feedback.php
│
├── Services/
│   ├── DocumentParserService.php
│   ├── ChunkingService.php
│   ├── EmbeddingService.php
│   └── VectorSearchService.php
│
database/
├── migrations/
└── seeders/

resources/
└── views/
    ├── layouts/
    ├── dashboard/
    ├── modules/
    ├── categories/
    ├── documents/
    ├── versions/
    ├── chat-logs/
    ├── feedback/
    └── auth/

routes/
├── web.php
└── api.php
```

---

# 33. Teknologi

## Web Admin

```text
PHP
Laravel
MySQL
Blade
Bootstrap / Tailwind CSS
JavaScript
Vite
```

## Document Processing

Diperlukan library/tool untuk:

```text
DOCX parsing
Text extraction
Text cleaning
Chunking
```

## Automation

```text
n8n
```

## Chatbot

```text
Telegram Bot API
```

## AI

Dapat menggunakan model AI yang didukung oleh n8n.

Contoh:

```text
OpenAI
Google Gemini
Anthropic
atau provider AI lainnya
```

## Vector Search

Menggunakan Vector Database atau vector storage yang kompatibel dengan arsitektur n8n.

---

# 34. Environment Variable

Contoh:

```env
APP_NAME=CIKA
APP_ENV=local
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cika
DB_USERNAME=root
DB_PASSWORD=

TELEGRAM_BOT_TOKEN=

N8N_WEBHOOK_URL=

AI_API_KEY=

VECTOR_DB_URL=
VECTOR_DB_API_KEY=
```

Credential asli tidak boleh dimasukkan ke repository public.

Gunakan:

```text
.env
```

---

# 35. Keamanan

Web Admin harus memiliki authentication.

Minimal:

```text
Login
Logout
Password Hashing
Session
Authorization
CSRF Protection
Input Validation
File Validation
API Authentication
```

File upload harus divalidasi.

Contoh:

```text
Allowed:
.docx
```

Validasi juga dapat mencakup:

```text
File extension
MIME type
Maximum file size
File name
Storage path
```

Token berikut tidak boleh ditulis langsung di source code:

```text
Telegram Bot Token
AI API Key
Database Password
Vector DB API Key
```

---

# 36. Scope Project

## Yang Dikerjakan

- Web Admin.
- Login Admin.
- Dashboard.
- Category Management.
- Module Management.
- Upload DOCX.
- Document Management.
- Module Versioning.
- Document Processing.
- Text Extraction.
- Chunking.
- Embedding.
- Vector Search.
- MySQL Database.
- Telegram Bot.
- Integrasi Telegram dengan n8n.
- Integrasi n8n dengan AI.
- RAG.
- Chat Log.
- Feedback.
- Module activation/deactivation.
- Replace Module.
- Re-processing Module.

## Yang Tidak Termasuk Tahap Awal

- Monitoring jaringan secara realtime.
- Otomatis mengubah konfigurasi MikroTik.
- Otomatis memperbaiki jaringan pelanggan.
- Billing pelanggan.
- Pembayaran online.
- Manajemen pelanggan ISP.
- Remote perangkat jaringan.
- Autonomous network operation.
- AI melakukan tindakan operasional tanpa persetujuan.
- Membuat atau mengubah isi SOP secara otomatis.

Fitur tersebut dapat dikembangkan sebagai project lanjutan.

---

# 37. Tahapan Pengerjaan

## Tahap 1 — Persiapan

Siapkan:

```text
PHP
Composer
Laravel
MySQL
Node.js
NPM
n8n
Telegram Bot
AI API
Git
Vector Database / Vector Storage
```

---

## Tahap 2 — Membuat Laravel

Buat project Laravel:

```text
CIKA Web Admin
```

Kemudian konfigurasi:

```text
.env
Database
Authentication
Storage
API
```

---

## Tahap 3 — Membuat Database

Buat database:

```text
cika
```

Migration utama:

```text
users
categories
modules
module_versions
documents
chat_logs
feedback
```

---

## Tahap 4 — Membuat Web Admin

Kerjakan:

```text
1. Login
2. Dashboard
3. Category
4. Module
5. Upload Document
6. Document Detail
7. Version
8. Processing Status
9. Chat Logs
10. Feedback
```

---

## Tahap 5 — Upload DOCX

Pastikan admin dapat:

```text
Upload
View
Replace
Delete
Re-process
Activate
Deactivate
```

file module.

---

## Tahap 6 — Document Processing

Buat proses:

```text
DOCX
 ↓
Extract Text
 ↓
Clean Text
 ↓
Detect Sections
 ↓
Chunking
```

---

## Tahap 7 — Embedding

Setiap chunk diproses:

```text
Chunk
 ↓
Embedding Model
 ↓
Vector
 ↓
Vector Database
```

---

## Tahap 8 — Vector Search

Implementasikan pencarian:

```text
Question
 ↓
Embedding
 ↓
Similarity Search
 ↓
Top Relevant Chunks
```

---

## Tahap 9 — Telegram Bot

Buat bot melalui Telegram BotFather.

Dapatkan:

```text
BOT TOKEN
```

Masukkan bot ke grup.

---

## Tahap 10 — Workflow n8n

Workflow:

```text
Telegram Trigger
       ↓
Check @CIKA
       ↓
Extract Question
       ↓
Vector Search
       ↓
Get Context
       ↓
AI Model
       ↓
Generate Answer
       ↓
Save Chat Log
       ↓
Telegram Reply
```

---

# 38. Testing

## Web Admin

```text
[ ] Login
[ ] Logout
[ ] Dashboard
[ ] Tambah category
[ ] Edit category
[ ] Delete category
[ ] Upload DOCX
[ ] Validasi file
[ ] Processing status
[ ] Module aktif
[ ] Module nonaktif
[ ] Replace document
[ ] Version module
[ ] Re-process document
[ ] Delete module
```

## Document Processing

```text
[ ] DOCX berhasil dibaca
[ ] Text berhasil diekstrak
[ ] Section berhasil dikenali
[ ] Chunk berhasil dibuat
[ ] Embedding berhasil dibuat
[ ] Data masuk Vector DB
[ ] Processing error dapat diketahui
```

## Telegram

```text
[ ] Bot menerima pesan
[ ] Bot mendeteksi @CIKA
[ ] Bot mengabaikan pesan tanpa @CIKA
[ ] Question berhasil diekstrak
[ ] Vector search berjalan
[ ] Context ditemukan
[ ] AI menghasilkan jawaban
[ ] Jawaban dikirim ke grup
[ ] Chat log tersimpan
```

---

# 39. Contoh Skenario Lengkap

Admin upload:

```text
Troubleshooting NOC.docx
```

Isi:

```text
PROSEDUR PELANGGAN INTERNET MATI

1. Periksa ID pelanggan.
2. Periksa status koneksi.
3. Periksa log MikroTik.
4. Periksa perangkat pelanggan.
5. Lakukan eskalasi apabila diperlukan.
```

Sistem:

```text
DOCX
 ↓
Extract
 ↓
Chunk
 ↓
Embedding
 ↓
Vector DB
```

Kemudian user bertanya:

```text
@CIKA gimana cara cek pelanggan yang mati?
```

n8n:

```text
Telegram
 ↓
Extract Question
 ↓
Semantic Search
 ↓
Find Relevant Chunks
 ↓
AI
```

CIKA:

```text
Untuk mengecek pelanggan yang mengalami
gangguan internet:

1. Periksa ID pelanggan.
2. Periksa status koneksi.
3. Periksa log MikroTik.
4. Periksa perangkat pelanggan.
5. Jika diperlukan, lakukan eskalasi.
```

---

# 40. Skenario Module Tidak Relevan

Misalnya module hanya berisi:

```text
Fiber Optic
```

tetapi user bertanya:

```text
@CIKA bagaimana cara melakukan billing pelanggan?
```

Jika tidak ditemukan context yang relevan:

```text
CIKA:

Maaf, CIKA belum menemukan panduan yang sesuai
dengan pertanyaan tersebut.

Silakan hubungi admin atau supervisor terkait.
```

AI tidak boleh mengarang SOP billing.

---

# 41. Skenario Update Module

Versi awal:

```text
Troubleshooting NOC v1.0
```

Admin mendapatkan dokumen terbaru:

```text
Troubleshooting NOC v2.0
```

Admin melakukan:

```text
Web Admin
 ↓
Module
 ↓
Replace File
 ↓
Upload v2.0
```

Sistem:

```text
v1.0
 ↓
Inactive

v2.0
 ↓
Extract
 ↓
Chunk
 ↓
Embedding
 ↓
Vector DB
 ↓
Active
```

CIKA kemudian menggunakan informasi dari versi aktif.

---

# 42. Monitoring

Web Admin dapat menyediakan halaman monitoring.

Contoh:

```text
Recent Questions

User       Question                  Module
--------------------------------------------------
Rafa       Cara cek pelanggan mati   NOC
Deni       Cara cek ODP              ODP
Ahmad      Cara splicing FO          Fiber Optic
```

---

# 43. Unanswered Questions

Sistem dapat mencatat pertanyaan ketika CIKA tidak menemukan knowledge.

Contoh:

```text
Unanswered Questions

1. Bagaimana prosedur X?
2. Cara melakukan Y?
3. Apa langkah Z?
```

Admin kemudian dapat menggunakan daftar tersebut untuk menentukan apakah perlu menambahkan atau memperbarui modul.

---

# 44. Pengembangan Selanjutnya

Setelah versi pertama stabil, CIKA dapat dikembangkan menjadi:

```text
CIKA V2
│
├── Advanced RAG
├── Hybrid Search
├── Re-ranking
├── Knowledge Versioning
├── Multi Admin
├── Role Permission
├── Analytics
├── Feedback System
├── Unanswered Question Analysis
├── Multi Group
├── Multi Department
├── OCR untuk PDF/Image
└── Dukungan format dokumen tambahan
```

---

# 45. Target Hasil

Setelah project selesai, diharapkan tersedia sistem:

```text
┌──────────────────────────────────────┐
│              WEB ADMIN               │
│                                      │
│ Upload DOCX                          │
│ Module Management                    │
│ Version Management                   │
│ Processing Monitoring                │
│ Chat Logs                            │
│ Feedback                             │
└──────────────────┬───────────────────┘
                   │
                   ▼
             ┌─────────────┐
             │    MYSQL    │
             │             │
             │ Metadata    │
             │ Modules     │
             │ Documents   │
             │ Users       │
             │ Logs        │
             └──────┬──────┘
                    │
                    ▼
             ┌─────────────┐
             │ DOCX        │
             │ PROCESSING  │
             └──────┬──────┘
                    │
                    ▼
             ┌─────────────┐
             │  CHUNKING   │
             └──────┬──────┘
                    │
                    ▼
             ┌─────────────┐
             │ EMBEDDING   │
             └──────┬──────┘
                    │
                    ▼
             ┌─────────────┐
             │ VECTOR DB   │
             └──────┬──────┘
                    │
                    ▼
             ┌─────────────┐
             │     n8n     │
             └──────┬──────┘
                    │
              ┌─────┴─────┐
              ▼           ▼
          AI MODEL    TELEGRAM BOT
              │           │
              └─────┬─────┘
                    ▼
             ┌─────────────┐
             │  TELEGRAM   │
             │    @CIKA    │
             └─────────────┘
```

---

# 46. Kesimpulan

CIKA dirancang sebagai **AI Knowledge Assistant berbasis dokumen** yang menghubungkan Web Admin, DOCX, MySQL, Document Processing, Vector Database, n8n, AI, dan Telegram.

Konsep utamanya:

```text
Admin Upload DOCX
        ↓
Document Processing
        ↓
Text Extraction
        ↓
Chunking
        ↓
Embedding
        ↓
Vector Database
        ↓
User bertanya @CIKA
        ↓
Semantic Search
        ↓
Relevant Module Content
        ↓
AI
        ↓
CIKA menjawab
```

Dengan arsitektur tersebut, admin tidak perlu mengetik knowledge secara manual. Modul cukup disediakan dalam bentuk DOCX dan sistem akan memprosesnya agar dapat digunakan sebagai sumber informasi CIKA.

Perubahan modul juga dapat dilakukan melalui Web Admin tanpa perlu mengubah source code chatbot atau membuat ulang workflow n8n.

Tahap awal sebaiknya berfokus pada:

```text
Upload DOCX
→ Extract Text
→ Chunking
→ Embedding
→ Vector Search
→ AI
→ Telegram
```

Setelah alur tersebut stabil, fitur seperti versioning, feedback, analytics, hybrid search, re-ranking, dan dukungan format dokumen tambahan dapat dikembangkan.
