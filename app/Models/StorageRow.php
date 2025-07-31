<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'rack_id',
        'row_number',
        'total_boxes',
        'available_boxes',
        'status'
    ];

    protected $casts = [
        'rack_id' => 'integer',
        'row_number' => 'integer',
        'total_boxes' => 'integer',
        'available_boxes' => 'integer',
    ];

    public function rack(): BelongsTo
    {
        return $this->belongsTo(StorageRack::class, 'rack_id');
    }

    public function boxes(): HasMany
    {
        return $this->hasMany(StorageBox::class, 'row_id');
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->total_boxes === 0) {
            return 0;
        }

        $usedBoxes = $this->total_boxes - $this->available_boxes;
        return round(($usedBoxes / $this->total_boxes) * 100, 2);
    }

    public function isFull(): bool
    {
        return $this->available_boxes === 0;
    }

    public function getNextAvailableBox()
    {
        return $this->boxes()
            ->where('status', 'available')
            ->orderBy('box_number')
            ->first();
    }
}
