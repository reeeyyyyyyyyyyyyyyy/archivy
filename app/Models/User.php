<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_type', // Add role_type for easy identification
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is staff (Pegawai TU)
     */
    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    /**
     * Check if user is intern (Mahasiswa)
     */
    public function isIntern(): bool
    {
        return $this->hasRole('intern');
    }

    /**
     * Get user role display name
     */
    public function getRoleDisplayName(): string
    {
        if ($this->isAdmin()) return 'Administrator';
        if ($this->isStaff()) return 'Pegawai TU';
        if ($this->isIntern()) return 'Mahasiswa Magang';
        return 'User';
    }

    /**
     * Get dashboard route based on role
     */
    public function getDashboardRoute(): string
    {
        if ($this->isAdmin()) return 'admin.dashboard';
        if ($this->isStaff()) return 'staff.dashboard';
        if ($this->isIntern()) return 'intern.dashboard';
        return 'admin.dashboard'; // fallback
    }

    /**
     * Get archives created by this user
     */
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class, 'created_by');
    }
}
