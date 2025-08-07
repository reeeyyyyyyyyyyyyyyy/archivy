# 🤖 Panduan Penggunaan Telegram Bot

## Cara Menggunakan Bot Telegram

### 1. Setup Awal

#### Langkah 1: Buat Bot Telegram
1. Buka Telegram, cari **@BotFather**
2. Kirim pesan: `/newbot`
3. Masukkan nama bot: `Arsip Digital Bot`
4. Masukkan username: `arsip_digital_bot` (harus berakhir dengan 'bot')
5. Simpan token yang diberikan

#### Langkah 2: Dapatkan Chat ID
1. Start bot yang baru dibuat
2. Kirim pesan ke bot: `/start`
3. Buka browser, akses: `https://api.telegram.org/bot<TOKEN>/getUpdates`
4. Cari `chat_id` di response JSON

#### Langkah 3: Konfigurasi Environment
Tambahkan ke file `.env`:
```env
TELEGRAM_BOT_TOKEN=your_telegram_bot_token_here
TELEGRAM_CHAT_ID=your_telegram_chat_id_here
```

### 2. Setup Webhook (Untuk Interaksi Real-time)

#### Langkah 1: Setup Webhook URL
```bash
# Setup webhook untuk production
php artisan telegram:setup-webhook "https://yourdomain.com/api/telegram/webhook"

# Atau untuk development dengan ngrok
php artisan telegram:setup-webhook "https://your-ngrok-url.ngrok.io/api/telegram/webhook"
```

#### Langkah 2: Test Webhook
```bash
# Test perintah help
php artisan telegram:test-webhook "/help"

# Test perintah status
php artisan telegram:test-webhook "/status"

# Test pencarian
php artisan telegram:test-webhook "surat keputusan"
```

### 3. Cara Penggunaan di Telegram

#### Perintah Dasar
```
/help - Menampilkan bantuan
/status - Status sistem arsip
/cari [kata kunci] - Cari arsip dengan perintah
```

#### Pencarian Langsung
Ketik kata kunci langsung tanpa perintah:
```
surat keputusan
001/2024
kepegawaian
anggaran
```

#### Pencarian dengan Perintah
Gunakan perintah `/cari`:
```
/cari surat keputusan
/cari 001/2024
/cari kepegawaian
```

### 4. Contoh Interaksi

#### Contoh 1: Bantuan
```
User: /help
Bot: 🤖 BOT ARSIP - BANTUAN

🔧 Perintah yang tersedia:

🔍 Pencarian:
• Ketik kata kunci langsung untuk mencari arsip
• /cari [kata kunci] - Cari arsip dengan perintah
• Contoh: /cari 001/2024

📊 Informasi:
• /status - Status sistem arsip
• /help - Tampilkan bantuan ini

💡 Tips pencarian:
• Gunakan nomor arsip (contoh: 001/2024)
• Gunakan kata kunci dari uraian arsip
• Gunakan nomor file
• Coba kata kunci yang lebih spesifik

📱 Contoh penggunaan:
• Ketik: surat keputusan
• Ketik: /cari 001/2024
• Ketik: kepegawaian
```

#### Contoh 2: Status Sistem
```
User: /status
Bot: 📊 STATUS SISTEM ARSIP

📁 Total Arsip: 150
🟢 Aktif: 100
🟡 Inaktif: 30
🔵 Permanen: 15
🔴 Musnah: 5

⏰ Update: 15/01/2024 10:30:25
🟢 Status: Sistem berjalan normal
```

#### Contoh 3: Pencarian Berhasil
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

📁 No. Arsip: 002/2024/005
📝 Uraian: Surat Keputusan tentang Kepegawaian
📂 Kategori: Kepegawaian
🏷️ Status: Aktif
📍 Lokasi: Rak 2, Box 3, File 1
📅 Tanggal: 10/01/2024
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

#### Contoh 4: Pencarian Tidak Ditemukan
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

### 5. Tips Penggunaan

#### Kata Kunci Efektif
- **Nomor Arsip**: `001/2024`, `002/2023`
- **Kata Kunci Uraian**: `surat keputusan`, `kepegawaian`, `anggaran`
- **Nomor File**: `001`, `002`, `003`

#### Pencarian Spesifik
- Gunakan kata kunci yang spesifik untuk hasil yang lebih akurat
- Kombinasikan kata kunci untuk hasil yang lebih baik
- Gunakan nomor arsip jika diketahui

#### Troubleshooting
- Jika tidak ada hasil, coba kata kunci yang lebih umum
- Pastikan arsip sudah ada di database
- Cek log Laravel untuk error

### 6. Commands untuk Development

#### Test Commands
```bash
# Test koneksi bot
php artisan telegram:test

# Test pencarian
php artisan telegram:test-search "surat keputusan"

# Test webhook
php artisan telegram:test-webhook "/help"

# Setup webhook
php artisan telegram:setup-webhook "https://yourdomain.com/api/telegram/webhook"
```

#### Manual Commands
```bash
# Kirim notifikasi retention
php artisan telegram:retention-alerts

# Kirim notifikasi maintenance
php artisan telegram:maintenance-notification

# Test status transition
php artisan telegram:test-status-transition
```

### 7. Setup untuk Production

#### Langkah 1: Setup Webhook
```bash
# Setup webhook untuk domain production
php artisan telegram:setup-webhook "https://arsip.domain.go.id/api/telegram/webhook"
```

#### Langkah 2: Test Webhook
```bash
# Test webhook berfungsi
php artisan telegram:test-webhook "/status"
```

#### Langkah 3: Monitor Logs
```bash
# Monitor log Laravel
tail -f storage/logs/laravel.log
```

### 8. Keamanan

#### Validasi Input
- Semua input divalidasi untuk mencegah SQL injection
- Kata kunci dibatasi maksimal 255 karakter
- Hasil pencarian dibatasi maksimal 10 item

#### Error Handling
- Error ditangkap dan dilog
- Pesan error yang user-friendly
- Tidak ada informasi sensitif yang terekspos

#### Rate Limiting
- Webhook diproses dengan cache untuk mencegah duplikasi
- Pesan yang sama tidak diproses berulang kali

### 9. Monitoring

#### Log Aktivitas
Semua pencarian dan error dilog di Laravel:
```php
Log::info('Telegram search', ['query' => $query, 'results' => $count]);
Log::error('Telegram search error', ['error' => $e->getMessage()]);
```

#### Monitoring
- Monitor jumlah pencarian per hari
- Monitor kata kunci yang sering dicari
- Monitor error rate

### 10. Troubleshooting

#### Bot tidak merespons
1. Cek token bot benar
2. Cek chat ID benar
3. Pastikan bot sudah di-start
4. Cek log Laravel untuk error

#### Webhook tidak berfungsi
1. Cek webhook URL benar
2. Cek server dapat diakses dari internet
3. Cek SSL certificate valid
4. Test webhook dengan command

#### Pencarian tidak menemukan hasil
1. Pastikan arsip sudah ada di database
2. Coba kata kunci yang lebih umum
3. Cek log Laravel untuk error
4. Pastikan database connection normal

### 11. Commands yang Akan Dihapus Saat Deploy

Commands berikut hanya untuk development/testing dan akan dihapus saat deploy:

```bash
# Commands yang akan dihapus
php artisan telegram:test-webhook
php artisan telegram:test-search
php artisan telegram:setup-webhook
php artisan telegram:webhook
```

Commands yang akan dipertahankan:
```bash
# Commands yang akan dipertahankan
php artisan telegram:test
php artisan telegram:retention-alerts
php artisan telegram:maintenance-notification
php artisan telegram:test-status-transition
```

### 12. Integrasi dengan Fitur Lain

#### Notifikasi Status
Fitur pencarian terintegrasi dengan notifikasi status transition yang sudah ada.

#### Database
Menggunakan model `Archive` yang sama dengan aplikasi web.

#### Authentication
Tidak memerlukan authentication karena menggunakan chat ID yang sudah dikonfigurasi.

---

**📝 Note**: Bot Telegram ini dirancang untuk kemudahan penggunaan. Pengguna dapat mencari arsip dengan cepat tanpa perlu membuka aplikasi web.

**🔒 Security**: Semua input divalidasi dan error ditangkap dengan aman.

**📊 Monitoring**: Semua aktivitas dilog untuk monitoring dan troubleshooting. 
