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
        'skkd',
        'box_number',
        'file_number',
        'rack_number',
        'row_number',
        're_evaluation',
        'is_manual_input',
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
        'manual_status_override',
        'manual_override_at',
        'manual_override_by',
        'created_by',
        'updated_by',
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
        $maxFileNumber = static::where('box_number', $boxNumber)->max('file_number');
        return $maxFileNumber ? $maxFileNumber + 1 : 1;
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
}
