<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
    ];

    public function classifications(): HasMany
    {
        return $this->hasMany(Classification::class);
    }

    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }
}
