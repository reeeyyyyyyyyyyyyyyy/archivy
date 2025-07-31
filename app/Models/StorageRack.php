<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageRack extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'total_rows',
        'total_boxes',
        'capacity_per_box',
        'status'
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'total_boxes' => 'integer',
        'capacity_per_box' => 'integer',
    ];

    public function rows(): HasMany
    {
        return $this->hasMany(StorageRow::class, 'rack_id');
    }

    public function boxes(): HasMany
    {
        return $this->hasMany(StorageBox::class, 'rack_id');
    }

    public function capacitySettings(): HasMany
    {
        return $this->hasMany(StorageCapacitySetting::class, 'rack_id');
    }

    public function getAvailableBoxesCount(): int
    {
        return $this->boxes()->where('status', 'available')->count();
    }

    public function getPartiallyFullBoxesCount(): int
    {
        return $this->boxes()->where('status', 'partially_full')->count();
    }

    public function getFullBoxesCount(): int
    {
        return $this->boxes()->where('status', 'full')->count();
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->total_boxes === 0) {
            return 0;
        }

        $usedBoxes = $this->total_boxes - $this->getAvailableBoxesCount();
        return round(($usedBoxes / $this->total_boxes) * 100, 2);
    }

    public function isFull(): bool
    {
        return $this->getAvailableBoxesCount() === 0;
    }

    public function getNextAvailableBox()
    {
        // First try to find partially full boxes that can still accept archives
        $partiallyFullBox = $this->boxes()
            ->where('status', 'partially_full')
            ->whereRaw('archive_count < capacity')
            ->orderBy('box_number')
            ->first();

        if ($partiallyFullBox) {
            return $partiallyFullBox;
        }

        // If no partially full boxes, find the first available box
        return $this->boxes()
            ->where('status', 'available')
            ->orderBy('box_number')
            ->first();
    }

    public function getNextAvailableRow()
    {
        return $this->rows()
            ->where('status', 'available')
            ->orderBy('row_number')
            ->first();
    }
}
