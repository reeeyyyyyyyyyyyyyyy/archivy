<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageCapacitySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'rack_id',
        'default_capacity_per_box',
        'warning_threshold',
        'auto_assign'
    ];

    protected $casts = [
        'rack_id' => 'integer',
        'default_capacity_per_box' => 'integer',
        'warning_threshold' => 'integer',
        'auto_assign' => 'boolean',
    ];

    public function rack(): BelongsTo
    {
        return $this->belongsTo(StorageRack::class, 'rack_id');
    }
}
