<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
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
     * Get archive route based on role
     */
    public function getArchiveRoute(string $type = 'index'): string
    {
        if ($this->isAdmin()) return "admin.archives.{$type}";
        if ($this->isStaff()) return "staff.archives.{$type}";
        if ($this->isIntern()) return "intern.archives.{$type}";
        return "admin.archives.{$type}"; // fallback
    }

    /**
     * Get search route based on role
     */
    public function getSearchRoute(): string
    {
        if ($this->isAdmin()) return 'admin.search.index';
        if ($this->isStaff()) return 'staff.search.index';
        if ($this->isIntern()) return 'intern.search.index';
        return 'admin.search.index'; // fallback
    }

    /**
     * Get bulk operations route based on role
     */
    public function getBulkRoute(): string
    {
        if ($this->isAdmin()) return 'admin.bulk.index';
        if ($this->isStaff()) return 'staff.bulk.index';
        if ($this->isIntern()) return 'intern.bulk.index';
        return 'admin.bulk.index'; // fallback
    }

    /**
     * Get storage management route based on role
     */
    public function getStorageRoute(): string
    {
        if ($this->isAdmin()) return 'admin.storage-management.index';
        if ($this->isStaff()) return 'staff.storage-management.index';
        if ($this->isIntern()) return 'intern.storage-management.index';
        return 'admin.storage-management.index'; // fallback
    }

    /**
     * Get export route based on role
     */
    public function getExportRoute(): string
    {
        if ($this->isAdmin()) return 'admin.export.index';
        if ($this->isStaff()) return 'staff.export.index';
        if ($this->isIntern()) return 'intern.export.index';
        return 'admin.export.index'; // fallback
    }

    /**
     * Get generate labels route based on role
     */
    public function getGenerateLabelsRoute(): string
    {
        if ($this->isAdmin()) return 'admin.generate-labels.index';
        if ($this->isStaff()) return 'staff.generate-labels.index';
        if ($this->isIntern()) return 'intern.generate-labels.index';
        return 'admin.generate-labels.index'; // fallback
    }

    /**
     * Get reports route based on role
     */
    public function getReportsRoute(): string
    {
        if ($this->isAdmin()) return 'admin.reports.retention-dashboard';
        if ($this->isStaff()) return 'staff.reports.retention-dashboard';
        if ($this->isIntern()) return 'intern.reports.retention-dashboard';
        return 'admin.reports.retention-dashboard'; // fallback
    }

    /**
     * Get re-evaluation route based on role
     */
    public function getReEvaluationRoute(): string
    {
        if ($this->isAdmin()) return 'admin.re-evaluation.index';
        if ($this->isStaff()) return 'staff.re-evaluation.index';
        if ($this->isIntern()) return 'intern.re-evaluation.index';
        return 'admin.re-evaluation.index'; // fallback
    }

    /**
     * Get archives created by this user
     */
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class, 'created_by');
    }
}
