# 🔍 Telegram Bot Search Feature

## Overview

Fitur pencarian arsip melalui Telegram Bot memungkinkan pengguna untuk mencari arsip dengan cepat menggunakan chat Telegram. Fitur ini sangat berguna untuk mencari arsip tanpa perlu membuka aplikasi web.

## Fitur Utama

### 1. Pencarian Langsung
Ketik kata kunci langsung di chat Telegram tanpa perlu perintah khusus:
```
surat keputusan
001/2024
kepegawaian
```

### 2. Pencarian dengan Perintah
Gunakan perintah `/cari` diikuti kata kunci:
```
/cari surat keputusan
/cari 001/2024
/cari kepegawaian
```

### 3. Perintah Bantuan
- `/help` - Menampilkan bantuan lengkap
- `/status` - Status sistem arsip

## Cara Kerja

### 1. Pencarian Database
Bot akan mencari di database berdasarkan:
- **Nomor Arsip** (`index_number`)
- **Uraian** (`description`)
- **Nomor File** (`file_number`)

### 2. Hasil Pencarian
Bot akan menampilkan maksimal 10 hasil dengan informasi:
- Nomor Arsip
- Uraian
- Kategori
- Status
- Lokasi (Rak, Box, File) - jika tersedia
- Tanggal pembuatan

### 3. Pesan Tidak Ditemukan
Jika tidak ada hasil, bot akan menampilkan:
- Pesan tidak ditemukan
- Tips pencarian
- Daftar perintah yang tersedia

## Contoh Penggunaan

### Contoh 1: Pencarian dengan Kata Kunci
```
User: surat keputusan
Bot: 🔍 HASIL PENCARIAN ARSIP

🔎 Kata kunci: surat keputusan
📊 Ditemukan: 3 arsip

📁 No. Arsip: 001/2024/001
📝 Uraian: Surat Keputusan Gubernur tentang...
📂 Kategori: Surat Menyurat
🏷️ Status: Aktif
📍 Lokasi: Rak 1, Box 5, File 3
📅 Tanggal: 15/01/2024
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### Contoh 2: Pencarian dengan Perintah
```
User: /cari 001/2024
Bot: 🔍 HASIL PENCARIAN ARSIP

🔎 Kata kunci: 001/2024
📊 Ditemukan: 1 arsip

📁 No. Arsip: 001/2024/001
📝 Uraian: Dokumen arsip penting
📂 Kategori: Surat Menyurat
🏷️ Status: Aktif
📍 Lokasi: Rak 1, Box 5, File 3
📅 Tanggal: 15/01/2024
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### Contoh 3: Tidak Ditemukan
```
User: dokumen tidak ada
Bot: 🔍 PENCARIAN ARSIP

❌ Tidak ditemukan arsip dengan kata kunci: dokumen tidak ada

💡 Tips pencarian:
• Gunakan nomor arsip (contoh: 001/2024)
• Gunakan kata kunci dari uraian
• Gunakan nomor file
• Coba kata kunci yang lebih umum

🔧 Perintah tersedia:
/help - Bantuan
/status - Status sistem
/cari [kata kunci] - Cari arsip
```

## Setup dan Konfigurasi

### 1. Pastikan Bot Token dan Chat ID Terkonfigurasi
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```

### 2. Test Fitur Pencarian
```bash
# Test pencarian dengan kata kunci
php artisan telegram:test-search "surat keputusan"

# Test webhook untuk interaksi real-time
php artisan telegram:webhook
```

### 3. Setup Webhook (Opsional)
Untuk interaksi real-time, setup webhook:

```bash
# Set webhook URL
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
     -H "Content-Type: application/json" \
     -d '{"url": "https://yourdomain.com/api/telegram/webhook"}'

# Jalankan webhook handler secara berkala (cron job)
* * * * * cd /path/to/your/project && php artisan telegram:webhook
```

## Commands yang Tersedia

### 1. Test Commands
```bash
# Test koneksi bot
php artisan telegram:test

# Test pencarian
php artisan telegram:test-search "kata kunci"

# Test webhook
php artisan telegram:webhook
```

### 2. Search Commands
```bash
# Pencarian langsung
php artisan telegram:search "kata kunci"
```

## Tips Penggunaan

### 1. Kata Kunci Efektif
- **Nomor Arsip**: `001/2024`, `002/2023`
- **Kata Kunci Uraian**: `surat keputusan`, `kepegawaian`, `anggaran`
- **Nomor File**: `001`, `002`, `003`

### 2. Pencarian Spesifik
- Gunakan kata kunci yang spesifik untuk hasil yang lebih akurat
- Kombinasikan kata kunci untuk hasil yang lebih baik
- Gunakan nomor arsip jika diketahui

### 3. Troubleshooting
- Jika tidak ada hasil, coba kata kunci yang lebih umum
- Pastikan arsip sudah ada di database
- Cek log Laravel untuk error

## Keamanan

### 1. Validasi Input
- Semua input divalidasi untuk mencegah SQL injection
- Kata kunci dibatasi maksimal 255 karakter
- Hasil pencarian dibatasi maksimal 10 item

### 2. Error Handling
- Error ditangkap dan dilog
- Pesan error yang user-friendly
- Tidak ada informasi sensitif yang terekspos

### 3. Rate Limiting
- Webhook diproses dengan cache untuk mencegah duplikasi
- Pesan yang sama tidak diproses berulang kali

## Monitoring dan Logging

### 1. Log Aktivitas
Semua pencarian dan error dilog di Laravel:
```php
Log::info('Telegram search', ['query' => $query, 'results' => $count]);
Log::error('Telegram search error', ['error' => $e->getMessage()]);
```

### 2. Monitoring
- Monitor jumlah pencarian per hari
- Monitor kata kunci yang sering dicari
- Monitor error rate

## Integrasi dengan Fitur Lain

### 1. Notifikasi Status
Fitur pencarian terintegrasi dengan notifikasi status transition yang sudah ada.

### 2. Database
Menggunakan model `Archive` yang sama dengan aplikasi web.

### 3. Authentication
Tidak memerlukan authentication karena menggunakan chat ID yang sudah dikonfigurasi.

## Roadmap

### Fitur yang Akan Ditambahkan
1. **Pencarian Lanjutan**: Filter berdasarkan kategori, status, tanggal
2. **Export Hasil**: Kirim hasil pencarian dalam format PDF
3. **Favorit**: Simpan pencarian favorit
4. **Notifikasi Pencarian**: Notifikasi ketika arsip yang dicari berubah status
5. **Multi-language**: Dukungan bahasa Inggris

### Optimasi
1. **Caching**: Cache hasil pencarian untuk performa lebih baik
2. **Indexing**: Optimasi database indexing untuk pencarian
3. **Fuzzy Search**: Pencarian yang toleran terhadap typo 
