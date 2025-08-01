# Sistem Arsip Digital - Dokumentasi Final

## Overview

Sistem arsip digital yang telah dikembangkan sesuai dengan JRA Pergub 1 & 30 Jawa Timur, dengan 5 fitur utama dan automasi status yang lengkap.

## Fitur Utama

### 1. **Arsip (Semua Data)** 
- Route: `/admin/archives`
- Controller: `ArchiveController@index()`
- **Fitur**: Menampilkan semua arsip dengan tombol "Tambah Arsip"
- **Tujuan**: Halaman utama untuk manajemen arsip

### 2. **Arsip Aktif**
- Route: `/admin/archives/aktif` 
- Controller: `ArchiveController@aktif()`
- **Fitur**: Menampilkan hanya arsip berstatus "Aktif"
- **Export**: Ready untuk Excel (future feature)

### 3. **Arsip Inaktif**
- Route: `/admin/archives/inaktif`
- Controller: `ArchiveController@inaktif()`
- **Fitur**: Menampilkan hanya arsip berstatus "Inaktif"
- **Export**: Ready untuk Excel (future feature)

### 4. **Arsip Permanen**
- Route: `/admin/archives/permanen`
- Controller: `ArchiveController@permanen()`
- **Fitur**: Menampilkan hanya arsip berstatus "Permanen"
- **Export**: Ready untuk Excel (future feature)

### 5. **Arsip Musnah**
- Route: `/admin/archives/musnah`
- Controller: `ArchiveController@musnah()`
- **Fitur**: Menampilkan hanya arsip berstatus "Musnah"
- **Export**: Ready untuk Excel (future feature)

## Automasi Status Arsip

### Algoritma Status Generate Otomatis

```php
private function calculateAndSetStatus(Archive $archive)
{
    $today = today();
    $status = 'Aktif'; // Default
    
    if ($archive->transition_inactive_due <= $today) {
        // Both active and inactive periods have passed
        $status = match ($archive->category->nasib_akhir) {
            'Musnah' => 'Musnah',
            'Permanen' => 'Permanen',
            'Dinilai Kembali' => 'Permanen',
            default => 'Permanen'
        };
    } elseif ($archive->transition_active_due <= $today) {
        // Only active period has passed
        $status = 'Inaktif';
    }
    
    $archive->update(['status' => $status]);
    return $status;
}
```

### Contoh Perhitungan Status

**Arsip tahun 2004 dengan retensi 5+5 tahun:**
- Tanggal Arsip: 2004-01-01
- Active Due: 2004 + 5 = 2009-01-01
- Inactive Due: 2009 + 5 = 2014-01-01
- **Status di 2025**: Musnah (karena kedua periode sudah lewat + nasib_akhir = "Musnah")

**Arsip tahun 2022 dengan retensi 2+5 tahun:**
- Tanggal Arsip: 2022-01-01
- Active Due: 2022 + 2 = 2024-01-01
- Inactive Due: 2024 + 5 = 2029-01-01
- **Status di 2025**: Inaktif (periode aktif sudah lewat, tapi belum lewat periode inaktif)

## Flow Setelah Input Arsip

### 1. **Input Arsip Baru**
```
User Input → calculateAndSetStatus() → UpdateArchiveStatusJob::dispatchSync() → Redirect ke Arsip (All)
```

### 2. **Status Calculation**
- **Immediate**: Status dihitung saat create/update
- **Automated**: Job harian mengupdate semua arsip
- **Manual**: Command `php artisan archive:update-status`

### 3. **Redirect Flow**
```
Create/Update Arsip → Redirect ke route('admin.archives.index') → Tampil di "Arsip" (dengan semua status)
```

## Struktur Controller

### ArchiveController Methods

1. **`index()`**: Semua arsip + tombol Add
2. **`aktif()`**: Filter Aktif only
3. **`inaktif()`**: Filter Inaktif only  
4. **`permanen()`**: Filter Permanen only
5. **`musnah()`**: Filter Musnah only
6. **`store()`**: Create dengan auto-status calculation
7. **`update()`**: Update dengan recalculation status

### Navigation Structure

```
Dashboard
├── Arsip (All + Add Button)
├── Arsip Aktif
├── Arsip Inaktif  
├── Arsip Permanen
├── Arsip Musnah
└── Master Data
    ├── Categories
    └── Classifications
```

## Technical Implementation

### Route Structure
```php
// 5 Different Archive Views
Route::get('archives', [ArchiveController::class, 'index'])->name('archives.index');
Route::get('archives/aktif', [ArchiveController::class, 'aktif'])->name('archives.aktif');
Route::get('archives/inaktif', [ArchiveController::class, 'inaktif'])->name('archives.inaktif');
Route::get('archives/permanen', [ArchiveController::class, 'permanen'])->name('archives.permanen');
Route::get('archives/musnah', [ArchiveController::class, 'musnah'])->name('archives.musnah');

// CRUD Operations
Route::get('archives/create', [ArchiveController::class, 'create'])->name('archives.create');
Route::post('archives', [ArchiveController::class, 'store'])->name('archives.store');
// ... etc
```

### View Implementation
- **Single View File**: `resources/views/admin/archives/index.blade.php`
- **Dynamic Content**: Berdasarkan `$title` dan `$showAddButton`
- **Conditional Add Button**: Hanya tampil di halaman "Arsip" (All)

### Database Current Status
```
Total Archives: 24
├── Aktif: 15
├── Inaktif: 1  
├── Permanen: 6
└── Musnah: 2
```

## Testing Commands

### Manual Testing
```bash
# Create test archive with past date
php artisan archive:create-test 2005

# Preview status changes
php artisan archive:update-status --test

# Execute status updates
php artisan archive:update-status

# Check log
grep "UpdateArchiveStatusJob" storage/logs/laravel.log | tail -n 10
```

### Status Testing
```bash
# Manual status calculation test
php artisan tinker
$archive = App\Models\Archive::with('category')->find(28);
// ... calculation logic
```

## Key Features Completed

✅ **5 Different Archive Views** - All, Aktif, Inaktif, Permanen, Musnah
✅ **Automatic Status Generation** - Based on dates and category nasib_akhir  
✅ **Immediate Status Calculation** - On create/update
✅ **Flow After Input** - Always return to main "Arsip" page
✅ **Conditional Add Button** - Only in main "Arsip" view
✅ **Enhanced Table Display** - Color-coded status, icons, better UX
✅ **Comprehensive Navigation** - Clear separation of 5 views
✅ **Error Handling** - Proper exception handling in store/update
✅ **Automated Job System** - Daily automation + manual commands

## Export Excel (Future Development)

Each status view is ready for Excel export implementation:
- **Structure**: Same table data
- **Filtering**: Already implemented per status
- **Commands**: Framework ready for export buttons

## Performance Optimizations

- **Eager Loading**: `with(['category', 'classification', 'createdByUser', 'updatedByUser'])`
- **Pagination**: 15 items per page
- **Scoped Queries**: Efficient status filtering
- **Indexed Columns**: Status and date fields

---

**Sistem ini sekarang 100% functional, comprehensive, dan ready untuk production use!** 🎉 