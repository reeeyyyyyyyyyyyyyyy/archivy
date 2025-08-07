# 🤖 Telegram Bot Setup Guide

## Setup Telegram Bot

### 1. Buat Bot Telegram

1. **Buka Telegram**, cari **@BotFather**
2. **Kirim pesan**: `/newbot`
3. **Masukkan nama bot**: `Arsip Digital Bot`
4. **Masukkan username**: `arsip_digital_bot` (harus berakhir dengan 'bot')
5. **BotFather akan kasih TOKEN**, simpan token tersebut!

**Contoh Token**: `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`

### 2. Dapatkan Chat ID

1. **Start bot** yang baru dibuat
2. **Kirim pesan** ke bot: `/start`
3. **Buka browser**, akses: `https://api.telegram.org/bot<TOKEN>/getUpdates`
4. **Cari `chat_id`** di response JSON

**Contoh Chat ID**: `123456789`

### 3. Konfigurasi Environment

Tambahkan ke file `.env`:

```env
TELEGRAM_BOT_TOKEN=your_telegram_bot_token_here
TELEGRAM_CHAT_ID=your_telegram_chat_id_here
```

### 4. Test Bot

```bash
# Test koneksi dasar
php artisan telegram:test

# Test pencarian arsip
php artisan telegram:test-search "surat keputusan"

# Test webhook (untuk interaksi real-time)
php artisan telegram:webhook
```

## Fitur Notifikasi

### 1. Status Transition Notifications (OTOMATIS)
- **Aktif → Inaktif**: Arsip yang berubah status dari Aktif ke Inaktif
- **Inaktif → Permanen**: Arsip yang berubah status dari Inaktif ke Permanen
- **Inaktif → Musnah**: Arsip yang berubah status dari Inaktif ke Musnah
- **Informasi Lokasi**: Rak, Box, dan File number (jika tersedia)
- **Dikirim otomatis** saat daily job berjalan (00:30 WIB)

### 2. Retention Alerts
- Arsip yang akan jatuh tempo dalam 7 hari
- Arsip yang akan jatuh tempo dalam 30 hari
- Dikirim setiap hari pukul 08:00 WIB

### 3. Maintenance Notifications
- Notifikasi maintenance rutin
- Dikirim setiap hari pukul 23:00 WIB

### 4. Storage Alerts
- Alert kapasitas storage
- Alert box yang hampir penuh

## Commands

```bash
# Test bot connection
php artisan telegram:test

# Test pencarian arsip
php artisan telegram:test-search "surat keputusan"

# Test webhook (untuk interaksi real-time)
php artisan telegram:webhook

# Test status transition notifications
php artisan telegram:test-status-transition

# Send retention alerts manually
php artisan telegram:retention-alerts

# Send maintenance notification manually
php artisan telegram:maintenance-notification
```

## Fitur Pencarian Arsip

Bot Telegram mendukung pencarian arsip dengan cara berikut:

### 1. Pencarian Langsung
Ketik kata kunci langsung di chat Telegram:
- `surat keputusan`
- `001/2024`
- `kepegawaian`

### 2. Perintah Pencarian
Gunakan perintah `/cari`:
- `/cari surat keputusan`
- `/cari 001/2024`
- `/cari kepegawaian`

### 3. Perintah Lainnya
- `/help` - Menampilkan bantuan
- `/status` - Status sistem arsip

### 4. Tips Pencarian
- Gunakan nomor arsip (contoh: 001/2024)
- Gunakan kata kunci dari uraian arsip
- Gunakan nomor file
- Coba kata kunci yang lebih spesifik untuk hasil yang lebih akurat

## Setup Webhook (Opsional)

Untuk interaksi real-time, Anda dapat setup webhook:

1. Set webhook URL di Telegram:
```bash
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
     -H "Content-Type: application/json" \
     -d '{"url": "https://yourdomain.com/api/telegram/webhook"}'
```

2. Jalankan webhook handler secara berkala:
```bash
# Tambahkan ke cron job
* * * * * cd /path/to/your/project && php artisan telegram:webhook
```

## Contoh Notifikasi Status Transition

```
🔄 TRANSISI STATUS ARSIP

📁 No. Arsip: 000.1/2023/001
📝 Uraian: Dokumen arsip penting
📂 Kategori: Surat Menyurat
🏷️ Status Lama: Aktif
🆕 Status Baru: Inaktif
🏗️ Rak: 1
📦 Box: 5
📄 File: 3
⏰ Waktu Transisi: 15/01/2024 00:30:25

Transisi otomatis berdasarkan JRA Pergub 1 & 30
```

## Troubleshooting

### Bot tidak mengirim pesan
1. Cek token bot benar
2. Cek chat ID benar
3. Pastikan bot sudah di-start
4. Cek log Laravel untuk error

### Error "Unauthorized"
- Token bot salah atau expired
- Buat bot baru dengan @BotFather

### Error "Chat not found"
- Chat ID salah
- Bot belum di-start oleh user
- User belum mengirim pesan ke bot

### Status transition tidak terkirim
1. Pastikan arsip memiliki lokasi (rak, box, file)
2. Cek daily job berjalan dengan `php artisan schedule:run`
3. Cek log Laravel untuk error 
