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
        'uraian',
        'kurun_waktu_start',
        'tingkat_perkembangan',
        'jumlah',
        'ket',
        'retention_active',
        'retention_inactive',
        'transition_active_due',
        'transition_inactive_due',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'kurun_waktu_start' => 'date',
        'transition_active_due' => 'date',
        'transition_inactive_due' => 'date',
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
}
