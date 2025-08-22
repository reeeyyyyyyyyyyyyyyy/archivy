# 🚀 Telegram Bot Quick Setup Guide

## ⚡ Setup dalam 5 Menit

### 1. Tambahkan ke file `.env`

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=your_telegram_bot_token_here
TELEGRAM_CHAT_ID=your_telegram_chat_id_here
```

### 2. Test Bot

```bash
# Test koneksi dan kirim pesan
php artisan telegram:test

# Test keyboard tombol
php artisan telegram:test --keyboard

# Test command tertentu
php artisan telegram:test --command=help
php artisan telegram:test --command=start

# Test dengan pesan custom
php artisan telegram:test --message="Halo dari ARSIPIN!"

# Test dengan chat ID custom
php artisan telegram:test --chat-id=123456789 --message="Test message"
```

### 3. Set Webhook (Untuk Bot Real-time)

```bash
# Set webhook ke domain Anda
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
     -H "Content-Type: application/json" \
     -d '{"url": "https://yourdomain.com/api/telegram/webhook"}'
```

### 4. Test Webhook

```bash
# Test webhook berfungsi
curl -X POST "https://yourdomain.com/api/telegram/webhook" \
     -H "Content-Type: application/json" \
     -d '{"message": {"chat": {"id": YOUR_CHAT_ID}, "text": "/start", "from": {"first_name": "Test"}}}'
```

## 🎯 Fitur yang Langsung Bisa Digunakan

### ✅ **Keyboard Interaktif**
- 6 tombol utama dengan emoji
- Mudah digunakan tanpa hafal command
- **Test dengan**: `php artisan telegram:test --keyboard`

### ✅ **Commands Cepat**
- `/start` - Mulai bot dengan keyboard
- `/help` - Bantuan lengkap dengan keyboard
- `/status` - Status sistem arsip
- `/search` - Menu pencarian
- `/retention` - Alert retensi
- `/storage` - Status storage
- `/website` - Status website
- `/keyboard` atau `/menu` - Tampilkan keyboard lagi

### ✅ **Pencarian Arsip Cerdas**
- Ketik kata kunci langsung
- Mencari di semua field (nomor, uraian, kategori, klasifikasi)
- Hasil lengkap dengan lokasi

### ✅ **Status Real-time**
- Total arsip, status breakdown
- Retensi mendekati
- Kapasitas storage
- Status website

## 🔧 Troubleshooting Cepat

### Bot tidak bisa dikirim pesan?
```bash
# Test koneksi
php artisan telegram:test
```

### Keyboard tidak muncul?
```bash
# Test keyboard
php artisan telegram:test --keyboard

# Atau kirim /start ke bot
```

### Webhook tidak berfungsi?
```bash
# Cek status webhook
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getWebhookInfo"
```

### Error "Unauthorized"?
- Token bot salah
- Buat bot baru dengan @BotFather

### Error "Chat not found"?
- Chat ID salah
- Bot belum di-start oleh user

## 📱 Cara Pakai Bot

1. **Start Bot**: Kirim `/start` ke bot
2. **Keyboard Muncul**: 6 tombol utama dengan emoji
3. **Cari Arsip**: Tekan "🔍 Cari Arsip" atau ketik langsung kata kunci
4. **Cek Status**: Tekan tombol status yang diinginkan
5. **Bantuan**: Tekan "❓ Bantuan" untuk panduan lengkap
6. **Reset Keyboard**: Kirim `/keyboard` atau `/menu`

## 🎉 Keunggulan

- **🎹 Keyboard Interaktif** - Mudah pakai
- **⌨️ Commands Cepat** - 8 command utama
- **🔍 Pencarian Cerdas** - Satu kata kunci, semua field
- **📊 Real-time Data** - Data langsung dari database
- **🚀 Auto-notification** - Notifikasi otomatis
- **📱 User-friendly** - Interface mudah dipahami
- **🔧 Easy Setup** - Setup dengan satu command
- **🧪 Easy Testing** - Test keyboard dan command

---

**🎯 Bot siap pakai untuk DPMPTSP Provinsi Jawa Timur!**
