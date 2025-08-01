<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classification extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'nama_klasifikasi',
        'retention_aktif',
        'retention_inaktif',
        'keterangan',
        'nasib_akhir',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }
}
