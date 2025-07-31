<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'rack_id',
        'row_id',
        'box_number',
        'archive_count',
        'capacity',
        'status'
    ];

    protected $casts = [
        'rack_id' => 'integer',
        'row_id' => 'integer',
        'box_number' => 'integer',
        'archive_count' => 'integer',
        'capacity' => 'integer',
    ];

    public function rack(): BelongsTo
    {
        return $this->belongsTo(StorageRack::class, 'rack_id');
    }

    public function row(): BelongsTo
    {
        return $this->belongsTo(StorageRow::class, 'row_id');
    }

    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class, 'box_number', 'box_number');
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->capacity === 0) {
            return 0;
        }

        return round(($this->archive_count / $this->capacity) * 100, 2);
    }

    public function isFull(): bool
    {
        return $this->archive_count >= $this->capacity;
    }

    public function isPartiallyFull(): bool
    {
        return $this->archive_count > 0 && $this->archive_count < $this->capacity;
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && !$this->isFull();
    }

    public function getNextFileNumber(): int
    {
        return $this->archive_count + 1;
    }

    public function updateStatus(): void
    {
        if ($this->isFull()) {
            $this->status = 'full';
        } elseif ($this->isPartiallyFull()) {
            $this->status = 'partially_full';
        } else {
            $this->status = 'available';
        }

        $this->save();
    }
}
