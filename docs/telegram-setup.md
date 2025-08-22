# 🤖 Telegram Bot Setup Guide - ARSIPIN

## 🚀 Setup Telegram Bot yang Mudah dan Lengkap

### 1. Buat Bot Telegram

1. **Buka Telegram**, cari **@BotFather**
2. **Kirim pesan**: `/newbot`
3. **Masukkan nama bot**: `ARSIPIN Bot`
4. **Masukkan username**: `arsipin_digital_bot` (harus berakhir dengan 'bot')
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
# Test koneksi dasar dan kirim pesan
php artisan telegram:test

# Test dengan pesan custom
php artisan telegram:test --message="Halo dari ARSIPIN!"

# Test dengan chat ID custom
php artisan telegram:test --chat-id=123456789 --message="Test message"
```

## 🎯 Fitur Bot yang Tersedia

### 1. **Keyboard Interaktif** 🎹
- **🔍 Cari Arsip** - Menu pencarian dengan tombol
- **📊 Status Sistem** - Informasi sistem real-time
- **⏰ Retensi Mendekati** - Alert arsip yang akan berubah status
- **📦 Kapasitas Storage** - Status penyimpanan
- **❓ Bantuan** - Panduan lengkap
- **🔄 Status Website** - Status website dan sistem

### 2. **Pencarian Arsip Cerdas** 🔍
- **Pencarian Langsung**: Ketik kata kunci (besar kecil tidak ngaruh)
- **Pencarian Kategori**: Surat Keputusan, Kepegawaian, Keuangan, Perizinan
- **Pencarian Multi-field**: Nomor arsip, uraian, klasifikasi, kategori
- **Hasil Lengkap**: Status, lokasi (rak, box, file), kategori, klasifikasi

### 3. **Commands Lengkap** ⌨️
- `/start` - Mulai bot dengan keyboard
- `/help` - Bantuan lengkap
- `/status` - Status sistem arsip
- `/search` - Menu pencarian
- `/retention` - Alert retensi
- `/storage` - Status storage
- `/website` - Status website

### 4. **Notifikasi Otomatis** 🔔
- **Status Transition**: Arsip yang berubah status (Aktif → Inaktif → Permanen/Musnah)
- **Retention Alerts**: Arsip yang akan jatuh tempo dalam 30 hari
- **Storage Alerts**: Kapasitas storage dan box yang hampir penuh

## 🛠️ Setup Webhook (Untuk Bot Real-time)

### 1. Set Webhook URL

```bash
# Set webhook ke domain Anda
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
     -H "Content-Type: application/json" \
     -d '{"url": "https://yourdomain.com/api/telegram/webhook"}'
```

### 2. Test Webhook

```bash
# Test webhook berfungsi
curl -X POST "https://yourdomain.com/api/telegram/webhook" \
     -H "Content-Type: application/json" \
     -d '{"message": {"chat": {"id": 123456789}, "text": "/start", "from": {"first_name": "Test"}}}'
```

### 3. Monitoring Webhook

```bash
# Cek log Laravel untuk melihat webhook
tail -f storage/logs/laravel.log

# Cek status webhook
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getWebhookInfo"
```

## 📱 Cara Penggunaan Bot

### 1. **Mulai Bot**
- Kirim `/start` ke bot
- Bot akan menampilkan keyboard dengan 6 tombol utama

### 2. **Cari Arsip**
- **Cara 1**: Tekan tombol "🔍 Cari Arsip" → Pilih kategori
- **Cara 2**: Ketik langsung kata kunci (contoh: "surat keputusan")
- **Cara 3**: Gunakan command `/search`

### 3. **Cek Status**
- **Status Sistem**: Tekan "📊 Status Sistem"
- **Retensi**: Tekan "⏰ Retensi Mendekati"
- **Storage**: Tekan "📦 Kapasitas Storage"
- **Website**: Tekan "🔄 Status Website"

### 4. **Bantuan**
- Tekan "❓ Bantuan" untuk panduan lengkap
- Atau gunakan command `/help`

## 🔧 Troubleshooting

### Bot tidak mengirim pesan
1. ✅ Cek token bot benar
2. ✅ Cek chat ID benar
3. ✅ Pastikan bot sudah di-start
4. ✅ Cek log Laravel untuk error

### Error "Unauthorized"
- Token bot salah atau expired
- Buat bot baru dengan @BotFather

### Error "Chat not found"
- Chat ID salah
- Bot belum di-start oleh user
- User belum mengirim pesan ke bot

### Webhook tidak berfungsi
1. ✅ Pastikan domain bisa diakses dari internet
2. ✅ Pastikan SSL (https) aktif
3. ✅ Cek firewall dan CORS
4. ✅ Test dengan `php artisan telegram:test`

### Status transition tidak terkirim
1. ✅ Pastikan arsip memiliki lokasi (rak, box, file)
2. ✅ Cek daily job berjalan dengan `php artisan schedule:run`
3. ✅ Cek log Laravel untuk error

## 📋 Contoh Penggunaan

### Pencarian Arsip
```
User: surat keputusan
Bot: 🔍 Hasil Pencarian: "surat keputusan"

📄 001/2024/001
📝 Surat Keputusan Kepala Dinas
🏷️ Surat Menyurat
📂 Surat Keputusan
📊 Status: Aktif
📍 Rak: 1, Box: 2, File: 15
```

### Status Sistem
```
📊 Status Sistem ARSIPIN

📁 Total Arsip: 1,250
🟢 Aktif: 800
🟡 Inaktif: 300
🔵 Permanen: 100
🔴 Musnah: 50

⏰ Update: 15/01/2024 10:30:25 WIB
```

### Alert Retensi
```
⏰ Retensi Mendekati

⚠️ 15 arsip akan berubah status dalam 30 hari ke depan.

📋 Detail:
• 001/2020/001 (25 hari lagi)
• 002/2020/001 (28 hari lagi)
• 003/2020/001 (30 hari lagi)
```

## 🎉 Keunggulan Bot Ini

1. **🎹 Keyboard Interaktif** - Mudah digunakan tanpa perlu hafal command
2. **🔍 Pencarian Cerdas** - Mencari di semua field dengan satu kata kunci
3. **📊 Real-time Data** - Data langsung dari database, bukan hardcode
4. **🚀 Auto-notification** - Notifikasi otomatis untuk status transition
5. **📱 User-friendly** - Interface yang mudah dipahami
6. **🔧 Easy Setup** - Setup dengan satu command
7. **📈 Scalable** - Bisa handle ribuan arsip dengan cepat

## 🚀 Deployment Checklist

- [ ] Bot token sudah diset di `.env`
- [ ] Chat ID sudah diset di `.env`
- [ ] Test bot dengan `php artisan telegram:test`
- [ ] Webhook URL sudah diset ke domain production
- [ ] SSL certificate aktif
- [ ] Firewall mengizinkan webhook
- [ ] Daily job berjalan untuk notifikasi otomatis

---

**🎯 Bot ini dirancang khusus untuk DPMPTSP Provinsi Jawa Timur dengan fitur yang sesuai kebutuhan pengelolaan arsip digital!** 
