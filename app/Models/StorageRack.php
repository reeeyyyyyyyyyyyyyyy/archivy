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
        'status',
        'year_start',
        'year_end'
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'total_boxes' => 'integer',
        'capacity_per_box' => 'integer',
        'year_start' => 'integer',
        'year_end' => 'integer',
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
        $capacity = $this->capacity_per_box;
        $n = $capacity;
        $halfN = $n / 2;

        // Count boxes that are empty (0 archives) OR have 1 to (N/2-1) archives
        $emptyBoxes = $this->boxes()->where('archive_count', 0)->count();
        $partiallyUsedAvailableBoxes = $this->boxes()->where('archive_count', '>=', 1)
            ->where('archive_count', '<', $halfN)
            ->count();

        return $emptyBoxes + $partiallyUsedAvailableBoxes;
    }

    public function getPartiallyFullBoxesCount(): int
    {
        $capacity = $this->capacity_per_box;
        $n = $capacity;
        $halfN = $n / 2;

        return $this->boxes()->where('archive_count', '>=', $halfN)
            ->where('archive_count', '<', $n)
            ->count();
    }

    public function getFullBoxesCount(): int
    {
        $capacity = $this->capacity_per_box;
        $n = $capacity;

        return $this->boxes()->where('archive_count', '>=', $n)->count();
    }

    public function getUtilizationPercentage(): float
    {
        $totalCapacity = $this->total_boxes * $this->capacity_per_box;

        if ($totalCapacity === 0) {
            return 0;
        }

        // Get actual archive count in this rack
        $actualArchiveCount = Archive::where('rack_number', $this->id)->count();

        return round(($actualArchiveCount / $totalCapacity) * 100, 2);
    }

    public function isFull(): bool
    {
        return $this->getAvailableBoxesCount() === 0;
    }

    public function getNextAvailableBox()
    {
        $capacity = $this->capacity_per_box;
        $n = $capacity;
        $halfN = $n / 2;

        // First try to find boxes with less than half capacity (available)
        $availableBox = $this->boxes()
            ->where('archive_count', '<', $halfN)
            ->orderBy('box_number')
            ->first();

        if ($availableBox) {
            return $availableBox;
        }

        // If no available boxes, find boxes with less than full capacity (partially full)
        $partiallyFullBox = $this->boxes()
            ->where('archive_count', '<', $n)
            ->orderBy('box_number')
            ->first();

        return $partiallyFullBox;
    }

    public function getNextAvailableRow()
    {
        return $this->rows()
            ->where('status', 'available')
            ->orderBy('row_number')
            ->first();
    }
}
