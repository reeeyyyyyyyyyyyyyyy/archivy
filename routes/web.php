<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Intern\DashboardController as InternDashboardController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\BulkOperationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/api/classifications', [ClassificationController::class, 'getFilteredClassifications'])->name('api.classifications');

Route::get('/', function () {
    if (Auth::check()) {
        // Redirect to appropriate dashboard based on role
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        } elseif ($user->isIntern()) {
            return redirect()->route('intern.dashboard');
        }
        return redirect()->route('admin.dashboard'); // fallback
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes - Full access
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Analytics - Admin only
    Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export-pdf', [App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export-pdf');
    Route::post('analytics/export-pdf', [App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export-pdf.post');

    // Archives - Full CRUD
    Route::get('archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('archives/aktif', [ArchiveController::class, 'aktif'])->name('archives.aktif');
    Route::get('archives/inaktif', [ArchiveController::class, 'inaktif'])->name('archives.inaktif');
    Route::get('archives/permanen', [ArchiveController::class, 'permanen'])->name('archives.permanen');
    Route::get('archives/musnah', [ArchiveController::class, 'musnah'])->name('archives.musnah');
    
    Route::get('archives/create', [ArchiveController::class, 'create'])->name('archives.create');
    Route::post('archives', [ArchiveController::class, 'store'])->name('archives.store');
    Route::get('archives/{archive}', [ArchiveController::class, 'show'])->name('archives.show');
    Route::get('archives/{archive}/edit', [ArchiveController::class, 'edit'])->name('archives.edit');
    Route::put('archives/{archive}', [ArchiveController::class, 'update'])->name('archives.update');
    Route::delete('archives/{archive}', [ArchiveController::class, 'destroy'])->name('archives.destroy');
    
    // Archive AJAX routes
    Route::get('archives/api/classification-details/{classification}', [ArchiveController::class, 'getClassificationDetails'])->name('archives.get-classification-details');
    Route::get('archives/api/classifications-by-category', [ArchiveController::class, 'getClassificationsByCategory'])->name('archives.get-classifications-by-category');
    Route::post('archives/change-status', [ArchiveController::class, 'changeStatus'])->name('archives.change-status');

    // Export routes
    Route::get('archives/export/{status?}', [ArchiveController::class, 'exportArchives'])->name('archives.export');
    Route::get('archives/export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('archives.export-form');
    Route::post('archives/export', [ArchiveController::class, 'export'])->name('archives.export.process');

    // Reports routes
    Route::get('reports/retention-dashboard', [ReportController::class, 'retentionDashboard'])->name('reports.retention-dashboard');

    // Search routes  
    Route::get('search', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
    Route::get('search/results', [App\Http\Controllers\SearchController::class, 'search'])->name('search.search');
    Route::get('search/autocomplete', [App\Http\Controllers\SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::get('search/export', [App\Http\Controllers\SearchController::class, 'exportResults'])->name('search.export');

    // Bulk Operations
    Route::get('bulk', [BulkOperationController::class, 'index'])->name('bulk.index');
    Route::get('bulk/get-archives', [BulkOperationController::class, 'getArchives'])->name('bulk.get-archives');
    Route::post('bulk/status-change', [BulkOperationController::class, 'bulkStatusChange'])->name('bulk.status-change');
    Route::post('bulk/assign-category', [BulkOperationController::class, 'bulkAssignCategory'])->name('bulk.assign-category');
    Route::post('bulk/assign-classification', [BulkOperationController::class, 'bulkAssignClassification'])->name('bulk.assign-classification');
    Route::post('bulk/delete', [BulkOperationController::class, 'bulkDelete'])->name('bulk.delete');
    Route::post('bulk/export', [BulkOperationController::class, 'bulkExport'])->name('bulk.export');

    // Master data routes - Admin only
    Route::resource('categories', CategoryController::class);
    Route::resource('classifications', ClassificationController::class);
    
    // Role Management - Admin only
    Route::get('roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'show'])->name('roles.show');
    Route::delete('roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('roles/assign-user', [App\Http\Controllers\Admin\RoleController::class, 'assignUser'])->name('roles.assign-user');
    Route::post('roles/remove-user', [App\Http\Controllers\Admin\RoleController::class, 'removeUser'])->name('roles.remove-user');
    
    // User Management - Admin only
    Route::get('users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    
    // Export Excel menu for admin
    Route::get('export', [ArchiveController::class, 'exportMenu'])->name('export.index');
    Route::get('export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('export-form');
    Route::post('export', [ArchiveController::class, 'export'])->name('export.process');
});

// Staff Routes - CRUD Archives + Analytics
Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    // Analytics access for staff
    Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export-pdf', [App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export-pdf');
    Route::post('analytics/export-pdf', [App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export-pdf.post');
    
    // Search routes for staff
    Route::get('search', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
    Route::get('search/results', [App\Http\Controllers\SearchController::class, 'search'])->name('search.search');
    Route::get('search/autocomplete', [App\Http\Controllers\SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::get('search/export', [App\Http\Controllers\SearchController::class, 'exportResults'])->name('search.export');
    
    // Export Excel menu for staff
    Route::get('export', [ArchiveController::class, 'exportMenu'])->name('export.index');
    Route::get('export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('export-form');
    Route::post('export', [ArchiveController::class, 'export'])->name('export.process');
});

// Intern Routes - CRUD Archives only (no analytics, no master data management)
Route::middleware(['auth', 'verified', 'role:intern'])->prefix('intern')->name('intern.')->group(function () {
    Route::get('/dashboard', [InternDashboardController::class, 'index'])->name('dashboard');
    
    // Search routes for intern
    Route::get('search', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
    Route::get('search/results', [App\Http\Controllers\SearchController::class, 'search'])->name('search.search');
    Route::get('search/autocomplete', [App\Http\Controllers\SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::get('search/export', [App\Http\Controllers\SearchController::class, 'exportResults'])->name('search.export');
    
    // Export Excel menu for intern
    Route::get('export', [ArchiveController::class, 'exportMenu'])->name('export.index');
    Route::get('export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('export-form');
    Route::post('export', [ArchiveController::class, 'export'])->name('export.process');
});

// Shared Routes - All roles can access archives CRUD (with different permissions)
Route::middleware(['auth', 'verified', 'role:admin|staff|intern'])->group(function () {
    // Archives routes shared by all roles
    Route::get('archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('archives/aktif', [ArchiveController::class, 'aktif'])->name('archives.aktif');
    Route::get('archives/inaktif', [ArchiveController::class, 'inaktif'])->name('archives.inaktif');
    Route::get('archives/permanen', [ArchiveController::class, 'permanen'])->name('archives.permanen');
    Route::get('archives/musnah', [ArchiveController::class, 'musnah'])->name('archives.musnah');
    
    Route::get('archives/create', [ArchiveController::class, 'create'])->name('archives.create');
    Route::post('archives', [ArchiveController::class, 'store'])->name('archives.store');
    Route::get('archives/{archive}', [ArchiveController::class, 'show'])->name('archives.show');
    Route::get('archives/{archive}/edit', [ArchiveController::class, 'edit'])->name('archives.edit');
    Route::put('archives/{archive}', [ArchiveController::class, 'update'])->name('archives.update');
    Route::delete('archives/{archive}', [ArchiveController::class, 'destroy'])->name('archives.destroy');
    
    // Archive AJAX routes for all roles
    Route::get('archives/api/classification-details/{classification}', [ArchiveController::class, 'getClassificationDetails'])->name('archives.get-classification-details');
    Route::get('archives/api/classifications-by-category', [ArchiveController::class, 'getClassificationsByCategory'])->name('archives.get-classifications-by-category');
    Route::post('archives/change-status', [ArchiveController::class, 'changeStatus'])->name('archives.change-status');

    // Export routes for all roles
    Route::get('archives/export/{status?}', [ArchiveController::class, 'exportArchives'])->name('archives.export');
    Route::get('archives/export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('archives.export-form');
    Route::post('archives/export', [ArchiveController::class, 'export'])->name('archives.export.process');
});

// API routes for AJAX
Route::middleware('auth')->group(function () {
    Route::get('/api/classifications', [ClassificationController::class, 'getFilteredClassifications']);
});

require __DIR__.'/auth.php';
