# Faker Data Generation - Dokumentasi

## Overview

Sistem faker data generation untuk testing dan development sistem arsip digital dengan data yang realistis sesuai dengan JRA Pergub Jawa Timur.

## Fitur Utama

### ✅ **Data Realistis**
- **Categories**: 10 kategori sesuai JRA Pergub Jawa Timur
- **Classifications**: 30 klasifikasi dengan kode hierarkis
- **Archives**: 100 arsip dengan data yang realistis
- **Users**: 3 user dengan role berbeda (admin, staff, intern)

### ✅ **Distribusi User**
- **Admin**: 40% arsip (40 arsip)
- **Staff**: 40% arsip (40 arsip)  
- **Intern**: 20% arsip (20 arsip)

### ✅ **Status Otomatis**
- **Perhitungan Status**: Berdasarkan tanggal retensi
- **Distribusi Realistis**: Aktif, Inaktif, Permanen, Musnah
- **Update Otomatis**: Menggunakan UpdateArchiveStatusJob

## Factory Components

### 1. **CategoryFactory**
**File**: `database/factories/CategoryFactory.php`

**Fitur**:
- 10 kategori sesuai JRA Pergub Jawa Timur
- Retention period yang realistis
- Nasib akhir yang sesuai (Musnah, Permanen, Dinilai Kembali)

**Kategori yang Dihasilkan**:
```php
[
    'Penyelenggaraan Pemerintahan' => ['retention' => '5+5', 'nasib' => 'Musnah'],
    'Kepegawaian' => ['retention' => '10+10', 'nasib' => 'Permanen'],
    'Keuangan' => ['retention' => '7+7', 'nasib' => 'Permanen'],
    'Perencanaan dan Pengembangan' => ['retention' => '8+8', 'nasib' => 'Permanen'],
    'Pelayanan Publik' => ['retention' => '3+3', 'nasib' => 'Musnah'],
    'Pengawasan dan Pengendalian' => ['retention' => '6+6', 'nasib' => 'Permanen'],
    'Kerjasama dan Hubungan Luar' => ['retention' => '4+4', 'nasib' => 'Dinilai Kembali'],
    'Infrastruktur dan Sarana Prasarana' => ['retention' => '9+9', 'nasib' => 'Permanen'],
    'Sosial dan Kesejahteraan' => ['retention' => '5+5', 'nasib' => 'Musnah'],
    'Lingkungan Hidup' => ['retention' => '7+7', 'nasib' => 'Permanen']
]
```

### 2. **ClassificationFactory**
**File**: `database/factories/ClassificationFactory.php`

**Fitur**:
- 30 klasifikasi dengan kode hierarkis (01.01, 02.01, dst)
- Otomatis terkait dengan kategori
- Retention period sesuai kategori

**Contoh Klasifikasi**:
```php
[
    'Rapat Koordinasi Pimpinan' => ['code' => '01.01', 'category' => 'Penyelenggaraan Pemerintahan'],
    'Pengangkatan Pegawai' => ['code' => '02.01', 'category' => 'Kepegawaian'],
    'Anggaran Pendapatan dan Belanja' => ['code' => '03.01', 'category' => 'Keuangan'],
    // ... dst
]
```

### 3. **ArchiveFactory**
**File**: `database/factories/ArchiveFactory.php`

**Fitur**:
- Data arsip yang realistis
- Index number otomatis (CODE/YEAR/MONTH/NUMBER)
- Tanggal retensi otomatis
- Distribusi user otomatis

**Data yang Dihasilkan**:
```php
[
    'index_number' => '01.01/2023/06/001',
    'description' => 'Laporan bulanan kegiatan administrasi dan pelayanan publik',
    'kurun_waktu_start' => '2023-06-15',
    'tingkat_perkembangan' => 'Asli|Salinan|Tembusan',
    'jumlah_berkas' => 1-50,
    'created_by' => 'admin|staff|intern'
]
```

## Seeder Components

### 1. **FakeDataSeeder**
**File**: `database/seeders/FakeDataSeeder.php`

**Fitur**:
- Seeder lengkap untuk semua data
- Distribusi user otomatis
- Update status otomatis
- Logging detail

### 2. **GenerateFakeDataCommand**
**File**: `app/Console/Commands/GenerateFakeDataCommand.php`

**Fitur**:
- Command line interface
- Opsi fleksibel untuk jumlah data
- Progress bar visual
- Summary report lengkap

## Penggunaan

### 1. **Menggunakan Seeder**
```bash
# Jalankan seeder
php artisan db:seed --class=FakeDataSeeder

# Atau melalui DatabaseSeeder
php artisan db:seed
```

### 2. **Menggunakan Command**
```bash
# Generate data default (100 arsip)
php artisan fake:generate

# Generate dengan opsi custom
php artisan fake:generate --archives=200 --categories=15 --classifications=50

# Generate dengan distribusi user custom
php artisan fake:generate --admin-percent=50 --staff-percent=30 --intern-percent=20

# Force recreate data
php artisan fake:generate --force

# Update status setelah generate
php artisan fake:generate --status-update
```

### 3. **Opsi Command Lengkap**
```bash
php artisan fake:generate \
    --categories=10 \
    --classifications=30 \
    --archives=100 \
    --admin-percent=40 \
    --staff-percent=40 \
    --intern-percent=20 \
    --force \
    --status-update
```

## Output dan Hasil

### 1. **Data yang Dihasilkan**
```
📊 Data Summary:
┌───────────────┬───────┐
│ Entity        │ Count │
├───────────────┼───────┤
│ Categories    │ 10    │
│ Classifications│ 30    │
│ Archives      │ 100   │
│ Users         │ 3     │
└───────────────┴───────┘
```

### 2. **Distribusi Status Arsip**
```
📈 Archive Status Distribution:
┌─────────┬───────┐
│ Status  │ Count │
├─────────┼───────┤
│ Aktif   │ 45    │
│ Inaktif │ 25    │
│ Permanen│ 20    │
│ Musnah  │ 10    │
└─────────┴───────┘
```

### 3. **Distribusi User**
```
👥 Archive Creation by User:
┌─────────────────┬──────┬───────┐
│ User            │ Role │ Count │
├─────────────────┼──────┼───────┤
│ Administrator   │ Admin│ 40    │
│ Staff TU        │ Staff│ 40    │
│ Mahasiswa Magang│ Intern│ 20   │
└─────────────────┴──────┴───────┘
```

### 4. **Login Credentials**
```
🔑 Login Credentials:
┌──────┬─────────────────────┬──────────┐
│ Role │ Email               │ Password │
├──────┼─────────────────────┼──────────┤
│ Admin│ admin@archivy.test  │ password │
│ Staff│ staff@archivy.test  │ password │
│ Intern│ intern@archivy.test│ password │
└──────┴─────────────────────┴──────────┘
```

## Testing dengan Data Faker

### 1. **Testing Status Transisi**
```bash
# Generate arsip dengan tanggal lama
php artisan fake:generate --archives=50

# Jalankan update status
php artisan archive:update-status

# Verifikasi perubahan status
php artisan tinker
>>> App\Models\Archive::select('status', DB::raw('count(*) as count'))->groupBy('status')->get();
```

### 2. **Testing User Permissions**
```bash
# Login sebagai admin
# Email: admin@archivy.test
# Password: password

# Login sebagai staff  
# Email: staff@archivy.test
# Password: password

# Login sebagai intern
# Email: intern@archivy.test
# Password: password
```

### 3. **Testing Search dan Filter**
```bash
# Test search dengan data faker
# Test filter berdasarkan kategori
# Test filter berdasarkan status
# Test filter berdasarkan user
```

## Customization

### 1. **Menambah Kategori Baru**
Edit `CategoryFactory.php`:
```php
$categories = [
    // ... existing categories
    [
        'nama_kategori' => 'Kategori Baru',
        'retention_aktif' => 5,
        'retention_inaktif' => 5,
        'nasib_akhir' => 'Musnah',
        'detailed_nasib_akhir' => 'Deskripsi kategori baru'
    ]
];
```

### 2. **Menambah Klasifikasi Baru**
Edit `ClassificationFactory.php`:
```php
$classifications = [
    // ... existing classifications
    [
        'nama_klasifikasi' => 'Klasifikasi Baru',
        'code' => '11.01',
        'retention_aktif' => 5,
        'retention_inaktif' => 5,
        'category_name' => 'Kategori Baru'
    ]
];
```

### 3. **Menambah Deskripsi Arsip**
Edit `ArchiveFactory.php`:
```php
$descriptions = [
    // ... existing descriptions
    'Dokumen baru untuk testing',
    'Arsip tambahan untuk development'
];
```

## Troubleshooting

### 1. **Error: Duplicate Entry**
```bash
# Clear existing data
php artisan fake:generate --force
```

### 2. **Error: User Not Found**
```bash
# Ensure roles exist
php artisan db:seed --class=RolesAndPermissionsSeeder

# Then generate fake data
php artisan fake:generate
```

### 3. **Error: Classification Not Found**
```bash
# Generate categories first
php artisan fake:generate --categories=10 --classifications=0 --archives=0

# Then generate classifications
php artisan fake:generate --categories=0 --classifications=30 --archives=0

# Finally generate archives
php artisan fake:generate --categories=0 --classifications=0 --archives=100
```

## Best Practices

### 1. **Development**
- Gunakan `--force` untuk fresh data
- Gunakan `--status-update` untuk testing automasi
- Monitor log untuk debugging

### 2. **Testing**
- Test dengan berbagai distribusi user
- Test dengan berbagai jumlah data
- Test status transisi otomatis

### 3. **Production**
- Jangan gunakan faker di production
- Backup data sebelum testing
- Monitor performance dengan data besar

---

**Sistem faker data generation siap untuk testing dan development!** 🚀 
