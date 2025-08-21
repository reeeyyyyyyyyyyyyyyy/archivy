<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'classification_id',
        'index_number',
        'description',
        'lampiran_surat',
        'kurun_waktu_start',
        'tingkat_perkembangan',
        'skkad',
        'box_number',
        'file_number',
        'rack_number',
        'row_number',
        're_evaluation',
        'is_manual_input',
        'definitive_number',
        'year_detected',
        'sort_order',
        'manual_retention_aktif',
        'manual_retention_inaktif',
        'manual_nasib_akhir',
        'jumlah_berkas',
        'ket',
        'retention_aktif',
        'retention_inaktif',
        'transition_active_due',
        'transition_inactive_due',
        'status',
        'evaluation_notes',
        'manual_status_override',
        'manual_override_at',
        'manual_override_by',
        'created_by',
        'updated_by',
        'parent_archive_id',
        'is_parent',
    ];

    protected $casts = [
        'kurun_waktu_start' => 'date',
        'transition_active_due' => 'date',
        'transition_inactive_due' => 'date',
        'manual_override_at' => 'datetime',
        'manual_status_override' => 'boolean',
        're_evaluation' => 'boolean',
        'is_manual_input' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function storageBox(): BelongsTo
    {
        return $this->belongsTo(StorageBox::class, 'box_number', 'box_number');
    }



    // Related archives relationships
    public function parentArchive(): BelongsTo
    {
        return $this->belongsTo(Archive::class, 'parent_archive_id');
    }

    public function relatedArchives()
    {
        return $this->hasMany(Archive::class, 'parent_archive_id');
    }

    // Get all related archives (including parent)
    public function getAllRelatedArchives()
    {
        // If this archive is a parent, get all its children + itself
        if ($this->is_parent) {
            return Archive::where('parent_archive_id', $this->id)
                ->orWhere('id', $this->id)
                ->orderBy('kurun_waktu_start', 'asc')
                ->get();
        }

        // If this archive has a parent, get all siblings + parent
        if ($this->parent_archive_id) {
            return Archive::where('parent_archive_id', $this->parent_archive_id)
                ->orWhere('id', $this->parent_archive_id)
                ->orderBy('kurun_waktu_start', 'asc')
                ->get();
        }

        // If no parent relationship, find archives with same attributes (show all roles for intern)
        $relatedArchives = Archive::where('category_id', $this->category_id)
            ->where('classification_id', $this->classification_id)
            ->where('lampiran_surat', $this->lampiran_surat)
            ->orderBy('kurun_waktu_start', 'asc')
            ->get();

        return $relatedArchives;
    }

    // Check if archive has same category/classification/attachment
    public function hasSameAttributes($otherArchive)
    {
        return $this->category_id === $otherArchive->category_id &&
               $this->classification_id === $otherArchive->classification_id &&
               $this->lampiran_surat === $otherArchive->lampiran_surat;
    }

    // Get parent archive (oldest year)
    public function getParentArchive()
    {
        if ($this->is_parent) return $this;
        if ($this->parentArchive) return $this->parentArchive;

        // Find parent by same attributes, oldest year
        return Archive::where('category_id', $this->category_id)
            ->where('classification_id', $this->classification_id)
            ->where('lampiran_surat', $this->lampiran_surat)
            ->orderBy('kurun_waktu_start')
            ->first();
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeInaktif($query)
    {
        return $query->where('status', 'Inaktif');
    }

    public function scopePermanen($query)
    {
        return $query->where('status', 'Permanen');
    }

    public function scopeMusnah($query)
    {
        return $query->where('status', 'Musnah');
    }

    public function scopeDinilaiKembali($query)
    {
        return $query->where('status', 'Dinilai Kembali');
    }

    /**
     * Get the formatted index number based on status and classification
     */
    public function getFormattedIndexNumberAttribute()
    {
        // Load relationships if not loaded
        if (!$this->relationLoaded('classification')) {
            $this->load('classification');
        }

        // For "Dinilai Kembali" status, return the full index_number
        if ($this->status == 'Dinilai Kembali') {
            return $this->index_number;
        }

        // For "LAINNYA" classification, return the manual index_number regardless of status
        if ($this->classification && $this->classification->code == 'LAINNYA') {
            return $this->index_number;
        }

        // For other classifications, return format: code/index_number/year
        if ($this->classification && $this->kurun_waktu_start) {
            return $this->classification->code . '/' . $this->index_number . '/' . $this->kurun_waktu_start->format('Y');
        }

        // Fallback to original index_number
        return $this->index_number;
    }

    /**
     * Scope for archives without storage location
     */
    public function scopeWithoutLocation($query)
    {
        return $query->whereNull('box_number')
                    ->orWhereNull('file_number')
                    ->orWhereNull('rack_number')
                    ->orWhereNull('row_number');
    }

    /**
     * Scope for archives with complete location
     */
    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('box_number')
                    ->whereNotNull('file_number')
                    ->whereNotNull('rack_number')
                    ->whereNotNull('row_number');
    }

    /**
     * Check if archive has complete storage location
     */
    public function hasStorageLocation()
    {
        return $this->box_number && $this->file_number && $this->rack_number && $this->row_number;
    }

    /**
     * Get formatted storage location
     */
    public function getStorageLocationAttribute()
    {
        if ($this->hasStorageLocation()) {
            return "Box: {$this->box_number}, File: {$this->file_number}, Rak: {$this->rack_number}, Baris: {$this->row_number}";
        }
        return 'Lokasi Belum di Set Pada Fitur Lokasi Penyimpanan';
    }

    /**
     * Get next box number
     */
    public static function getNextBoxNumber()
    {
        return static::max('box_number') + 1;
    }

    /**
     * Get next file number for a specific box
     */
    public static function getNextFileNumber($boxNumber)
    {
        // Get all existing file numbers for this box
        $existingFileNumbers = static::where('box_number', $boxNumber)
            ->pluck('file_number')
            ->sort()
            ->values();

        // If no archives in box, start with 1
        if ($existingFileNumbers->isEmpty()) {
            return 1;
        }

        // Find the first gap in file numbers
        $expectedFileNumber = 1;
        foreach ($existingFileNumbers as $existingFileNumber) {
            if ($existingFileNumber > $expectedFileNumber) {
                // Found a gap, return the missing number
                return $expectedFileNumber;
            }
            $expectedFileNumber = $existingFileNumber + 1;
        }

        // No gaps found, return the next number after the highest
        return $existingFileNumbers->max() + 1;
    }

    /**
     * Get next file number for a specific rack and box
     */
    public static function getNextFileNumberForRack($rackNumber, $boxNumber)
    {
        // Get all existing file numbers for this specific rack and box
        $existingFileNumbers = static::where('rack_number', $rackNumber)
            ->where('box_number', $boxNumber)
            ->pluck('file_number')
            ->sort()
            ->values();

        // If no archives in box, start with 1
        if ($existingFileNumbers->isEmpty()) {
            return 1;
        }

        // Find the first gap in file numbers
        $expectedFileNumber = 1;
        foreach ($existingFileNumbers as $existingFileNumber) {
            if ($existingFileNumber > $expectedFileNumber) {
                // Found a gap, return the missing number
                return $expectedFileNumber;
            }
            $expectedFileNumber = $existingFileNumber + 1;
        }

        // No gaps found, return the next number after the highest
        return $existingFileNumbers->max() + 1;
    }

    /**
     * Get next file number for a specific rack, box, and classification
     * File number berulang ke 1 saat pindah masalah (classification)
     * File number berlanjut sampai kapasitas box penuh, lalu pindah ke box berikutnya
     */
    public static function getNextFileNumberForClassification($rackNumber, $boxNumber, $classificationId, $year)
    {
        // Get existing file numbers for this specific rack, box, and classification (TIDAK per tahun)
        $existingFileNumbers = static::where('rack_number', $rackNumber)
            ->where('box_number', $boxNumber)
            ->where('classification_id', $classificationId)
            ->pluck('file_number')
            ->sort()
            ->values();

        // If no archives with same classification in this box, start with 1
        if ($existingFileNumbers->isEmpty()) {
            return 1;
        }

        // Find the first gap in file numbers
        $expectedFileNumber = 1;
        foreach ($existingFileNumbers as $existingFileNumber) {
            if ($existingFileNumber > $expectedFileNumber) {
                // Found a gap, return the missing number
                return $expectedFileNumber;
            }
            $expectedFileNumber = $existingFileNumber + 1;
        }

        // No gaps found, return the next number after the highest
        return $existingFileNumbers->max() + 1;
    }

    /**
     * Get next file number following the correct definitive number rules:
     * - File numbers continue across boxes for the same year and classification until year changes
     * - File numbers restart at 1 for new years within same classification
     * - File numbers restart at 1 for different classification
     */
    public static function getNextFileNumberCorrect($rackNumber, $boxNumber, $classificationId, $year)
    {
        // Get the highest file number for this classification and year across ALL boxes in this rack
        $maxFileNumberInYear = static::where('rack_number', $rackNumber)
            ->where('classification_id', $classificationId)
            ->whereYear('kurun_waktu_start', $year)
            ->max('file_number');

        // If no archives for this classification and year in this rack, start with 1
        if (is_null($maxFileNumberInYear)) {
            return 1;
        }

        // Return the next number after the highest for this year
        return $maxFileNumberInYear + 1;
    }

    /**
     * Fix existing file numbers to follow the correct definitive number rules
     * This method will update all existing archives to have correct file numbers
     */
    public static function fixAllExistingFileNumbers()
    {
        // Get all archives with storage locations
        $archives = static::whereNotNull('rack_number')
            ->whereNotNull('box_number')
            ->whereNotNull('file_number')
            ->orderBy('rack_number')
            ->orderBy('classification_id')
            ->orderBy('kurun_waktu_start')
            ->orderBy('box_number')
            ->get();

        $fixedCount = 0;
        $errors = [];

        // Group archives by rack, classification, and year (NOT by box)
        $groupedArchives = $archives->groupBy(function($archive) {
            return $archive->rack_number . '-' . $archive->classification_id . '-' . $archive->kurun_waktu_start->year;
        });

        foreach ($groupedArchives as $groupKey => $groupArchives) {
            // Sort archives within group by kurun_waktu_start, then by box_number
            $sortedArchives = $groupArchives->sortBy(function($archive) {
                return $archive->kurun_waktu_start->format('Y-m-d') . '-' . $archive->box_number;
            });

            $expectedFileNumber = 1;

            foreach ($sortedArchives as $archive) {
                $oldFileNumber = $archive->file_number;

                if ($oldFileNumber !== $expectedFileNumber) {
                    try {
                        $archive->update(['file_number' => $expectedFileNumber]);
                        $fixedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Failed to update archive {$archive->index_number}: " . $e->getMessage();
                    }
                }

                $expectedFileNumber++;
            }
        }

        return [
            'success' => true,
            'fixed_count' => $fixedCount,
            'errors' => $errors
        ];
    }

    /**
     * Scope for advanced search functionality
     */
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('index_number', 'ILIKE', "%{$term}%")
              ->orWhere('description', 'ILIKE', "%{$term}%")
              ->orWhere('ket', 'ILIKE', "%{$term}%")
              ->orWhereHas('category', function ($categoryQuery) use ($term) {
                  $categoryQuery->where('nama_kategori', 'ILIKE', "%{$term}%");
              })
              ->orWhereHas('classification', function ($classQuery) use ($term) {
                  $classQuery->where('nama_klasifikasi', 'ILIKE', "%{$term}%")
                            ->orWhere('code', 'ILIKE', "%{$term}%");
              });
        });
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        if (empty($categoryId)) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for filtering by classification
     */
    public function scopeByClassification($query, $classificationId)
    {
        if (empty($classificationId)) {
            return $query;
        }

        return $query->where('classification_id', $classificationId);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if (empty($startDate) && empty($endDate)) {
            return $query;
        }

        if ($startDate && $endDate) {
            return $query->whereBetween('kurun_waktu_start', [$startDate, $endDate]);
        } elseif ($startDate) {
            return $query->where('kurun_waktu_start', '>=', $startDate);
        } elseif ($endDate) {
            return $query->where('kurun_waktu_start', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope for filtering by created user
     */
    public function scopeByCreatedUser($query, $userId)
    {
        if (empty($userId)) {
            return $query;
        }

        return $query->where('created_by', $userId);
    }

    /**
     * Scope for filtering by year
     */
    public function scopeByYear($query, $year)
    {
        if (empty($year)) {
            return $query;
        }

        return $query->whereYear('kurun_waktu_start', $year);
    }

    /**
     * Scope for filtering by year range
     */
    public function scopeByYearRange($query, $startYear, $endYear)
    {
        if (empty($startYear) && empty($endYear)) {
            return $query;
        }

        if ($startYear && $endYear) {
            return $query->whereYear('kurun_waktu_start', '>=', $startYear)
                        ->whereYear('kurun_waktu_start', '<=', $endYear);
        } elseif ($startYear) {
            return $query->whereYear('kurun_waktu_start', '>=', $startYear);
        } elseif ($endYear) {
            return $query->whereYear('kurun_waktu_start', '<=', $endYear);
        }

        return $query;
    }

    /**
     * Scope for filtering archives approaching transition
     */
    public function scopeApproachingTransition($query, $days = 30)
    {
        // Convert string to integer if needed
        $days = is_numeric($days) ? (int) $days : 30;

        $today = today();
        $futureDate = $today->copy()->addDays($days);

        return $query->where(function ($q) use ($today, $futureDate) {
            $q->whereBetween('transition_active_due', [$today, $futureDate])
              ->orWhereBetween('transition_inactive_due', [$today, $futureDate]);
        });
    }

    /**
     * Get formatted definitive number display
     */
    public function getFormattedDefinitiveNumberAttribute()
    {
        if (!$this->definitive_number) {
            return 'Belum di-generate';
        }

        // If archive has storage location, format as Rack-Row-Box-File
        if ($this->rack_number && $this->row_number && $this->box_number && $this->file_number) {
            return "R{$this->rack_number}-R{$this->row_number}-B{$this->box_number}-F{$this->file_number}";
        }

        // If no storage location, return the simple number
        return (string) $this->definitive_number;
    }

    /**
     * Get definitive number breakdown (for debugging)
     */
    public function getDefinitiveNumberBreakdownAttribute()
    {
        if (!$this->definitive_number) {
            return null;
        }

        // If archive has storage location, show breakdown
        if ($this->rack_number && $this->row_number && $this->box_number && $this->file_number) {
            return [
                'rack' => $this->rack_number,
                'row' => $this->row_number,
                'box' => $this->box_number,
                'file' => $this->file_number,
                'formatted' => "R{$this->rack_number}-R{$this->row_number}-B{$this->box_number}-F{$this->file_number}"
            ];
        }

        return [
            'simple_number' => $this->definitive_number,
            'formatted' => (string) $this->definitive_number
        ];
    }


}
