# Sistem Automasi Status Arsip

## Overview

Sistem ini secara otomatis mengubah status arsip berdasarkan tanggal retensi sesuai dengan aturan JRA Pergub 1 & 30 Jawa Timur.

## Alur Status Automatis

```
Aktif -> Inaktif -> [Permanen/Musnah]
```

### 1. Transisi Aktif ke Inaktif
- **Kondisi**: `transition_active_due <= hari ini`
- **Proses**: Status berubah dari "Aktif" ke "Inaktif"
- **Perhitungan**: `transition_active_due = kurun_waktu_start + retention_active (dalam tahun)`

### 2. Transisi Inaktif ke Status Final
- **Kondisi**: `transition_inactive_due <= hari ini`
- **Proses**: Status berubah dari "Inaktif" ke status final berdasarkan `nasib_akhir` kategori:
  - `nasib_akhir = "Musnah"` → Status menjadi "Musnah"
  - `nasib_akhir = "Permanen"` → Status menjadi "Permanen"
  - `nasib_akhir = "Dinilai Kembali"` → Status menjadi "Permanen" (default)
- **Perhitungan**: `transition_inactive_due = transition_active_due + retention_inactive (dalam tahun)`

## Komponen Sistem

### 1. Job Processor: `UpdateArchiveStatusJob`
**File**: `app/Jobs/UpdateArchiveStatusJob.php`

**Fungsi**: 
- Mencari arsip yang sudah jatuh tempo untuk transisi status
- Mengupdate status sesuai dengan aturan bisnis
- Mencatat log lengkap untuk setiap perubahan

**Fitur Logging**:
- Log setiap transisi dengan detail ID, status lama/baru, tanggal due
- Log berdasarkan `nasib_akhir` dari kategori

### 2. Scheduler: `app/Console/Kernel.php`
**Konfigurasi**: Job dijalankan setiap hari jam 00:30
```php
$schedule->job(new UpdateArchiveStatusJob())->dailyAt('00:30');
```

**Untuk Testing**: Ubah ke `everyMinute()` untuk testing
```php
$schedule->job(new UpdateArchiveStatusJob())->everyMinute();
```

### 3. Command Interface: `UpdateArchiveStatusCommand`
**File**: `app/Console/Commands/UpdateArchiveStatusCommand.php`

**Penggunaan**:
```bash
# Jalankan update status
php artisan archive:update-status

# Preview perubahan tanpa eksekusi
php artisan archive:update-status --test
```

### 4. Testing Command: `CreateTestArchiveCommand`
**File**: `app/Console/Commands/CreateTestArchiveCommand.php`

**Penggunaan**:
```bash
# Buat arsip test dari tahun 2013
php artisan archive:create-test 2013
```

## Contoh Skenario Testing

### Arsip Tahun 2013 dengan Retensi 2+5 Tahun
1. **Data Arsip**:
   - Tanggal Arsip: 2013-01-01
   - Retensi Aktif: 2 tahun
   - Retensi Inaktif: 5 tahun

2. **Perhitungan**:
   - `transition_active_due`: 2013-01-01 + 2 tahun = 2015-01-01
   - `transition_inactive_due`: 2015-01-01 + 5 tahun = 2020-01-01

3. **Hasil di Tahun 2025**:
   - Kedua tanggal sudah lewat
   - Status langsung berubah: Aktif → Inaktif → Musnah/Permanen (sesuai kategori)

## Monitoring dan Logging

### 1. Log File
**Lokasi**: `storage/logs/laravel.log`

**Contoh Log Entry**:
```
[2025-07-21 20:18:18] local.INFO: UpdateArchiveStatusJob: Starting archive status update job
[2025-07-21 20:18:18] local.INFO: UpdateArchiveStatusJob: Today date string: 2025-07-21
[2025-07-21 20:18:18] local.INFO: UpdateArchiveStatusJob: Found 1 archives to transition from Aktif to Inaktif
[2025-07-21 20:18:18] local.INFO: UpdateArchiveStatusJob: Archive ID 25 transitioned from Aktif to Inaktif (due: 2015-01-01)
[2025-07-21 20:18:18] local.INFO: UpdateArchiveStatusJob: Found 1 archives to transition from Inaktif to final status
[2025-07-21 20:18:18] local.INFO: UpdateArchiveStatusJob: Archive ID 25 transitioned from Inaktif to Musnah based on category nasib_akhir: Musnah (due: 2020-01-01)
[2025-07-21 20:18:18] local.INFO: UpdateArchiveStatusJob: Archive status update job completed
```

### 2. Query Log
**Untuk Debug**:
```bash
grep "UpdateArchiveStatusJob" storage/logs/laravel.log | tail -n 10
```

## Setup Cron Job (Production)

### 1. Edit Crontab
```bash
crontab -e
```

### 2. Tambahkan Entry
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Verifikasi Schedule
```bash
php artisan schedule:list
```

## Testing dan Verifikasi

### 1. Test Manual
```bash
# Preview perubahan
php artisan archive:update-status --test

# Buat data test
php artisan archive:create-test 2010

# Jalankan job
php artisan archive:update-status

# Verifikasi log
grep "UpdateArchiveStatusJob" storage/logs/laravel.log | tail -n 5
```

### 2. Test Otomatis
- Buat arsip dengan tanggal masa lalu
- Jalankan job scheduler
- Verifikasi perubahan status di database

## Troubleshooting

### 1. Job Tidak Berjalan
- Periksa konfigurasi cron job
- Verifikasi permission file
- Check log scheduler: `php artisan schedule:list`

### 2. Status Tidak Berubah
- Periksa log aplikasi untuk error
- Verifikasi data tanggal retensi
- Test dengan `--test` flag untuk debug

### 3. Queue Jobs (Opsional)
Jika menggunakan queue, pastikan worker berjalan:
```bash
php artisan queue:work
```

## Kesimpulan

Sistem automasi status arsip telah berfungsi dengan sempurna:
- ✅ Deteksi otomatis arsip yang jatuh tempo
- ✅ Transisi status berdasarkan `nasib_akhir` kategori
- ✅ Logging lengkap untuk audit trail
- ✅ Command testing untuk verifikasi
- ✅ Scheduler harian untuk operasi produksi

Sistem ini memastikan compliance dengan aturan JRA dan mengotomatisasi proses yang sebelumnya manual. 