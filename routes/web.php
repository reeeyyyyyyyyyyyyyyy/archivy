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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RelatedArchivesController;
use App\Http\Controllers\Staff\StaffPersonalFilesController;

// API routes for AJAX (public)
Route::get('/api/classifications', [ClassificationController::class, 'getFilteredClassifications'])->name('api.classifications');

// Home route
Route::get('/', function () {
    if (Auth::check()) {
        // Redirect to appropriate dashboard based on role
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('staff')) {
            return redirect()->route('staff.dashboard');
        } elseif ($user->hasRole('intern')) {
            return redirect()->route('intern.dashboard');
        }
        return redirect()->route('admin.dashboard'); // fallback
    }
    return view('welcome');
});

// Profile routes - All authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================================
// ADMIN ROUTES - Full access
// ========================================
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Analytics - Admin only
    Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export-pdf', [App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export-pdf');
    Route::post('analytics/export-pdf', [App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export-pdf.post');

    // Archives - Full CRUD
    Route::get('archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('archives/parent', [ArchiveController::class, 'parentArchives'])->name('archives.parent');
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

    // Related Archives routes
    Route::get('archives/related/category', [RelatedArchivesController::class, 'byCategory'])->name('archives.related-category');
    Route::get('archives/{archive}/related', [RelatedArchivesController::class, 'index'])->name('archives.related');
    Route::get('archives/{parentArchive}/create-related', [RelatedArchivesController::class, 'createRelated'])->name('archives.create-related');
    Route::post('archives/{parentArchive}/store-related', [RelatedArchivesController::class, 'storeRelated'])->name('archives.store-related');
    Route::post('archives/bulk-update-location', [RelatedArchivesController::class, 'bulkUpdateLocation'])->name('archives.bulk-update-location');
    Route::get('archives/storage-management/{rack}/grid-data', [App\Http\Controllers\StorageManagementController::class, 'getGridData'])->name('archives.storage-management.grid-data');

    // Personal Files routes
    Route::resource('personal-files', App\Http\Controllers\Admin\PersonalFilesController::class);

    // Export routes
    Route::get('archives/export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('archives.export-form');
    Route::post('archives/export', [ArchiveController::class, 'export'])->name('archives.export.process');
    Route::get('archives/export/{status?}', [ArchiveController::class, 'exportArchives'])->name('archives.export');

    // Reports routes
    Route::get('reports/retention-dashboard', [ReportController::class, 'retentionDashboard'])->name('reports.retention-dashboard');

    // Search routes
    Route::get('search', [SearchController::class, 'index'])->name('search.index');
    Route::get('search/results', [SearchController::class, 'search'])->name('search.search');
    Route::get('search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::get('search/export', [SearchController::class, 'exportResults'])->name('search.export');

    // Bulk Operations
    Route::get('bulk', [BulkOperationController::class, 'index'])->name('bulk.index');
    Route::get('bulk/get-archives', [BulkOperationController::class, 'getArchives'])->name('bulk.get-archives');


    Route::post('bulk/status-change', [BulkOperationController::class, 'bulkStatusChange'])->name('bulk.status-change');
    Route::post('bulk/assign-category', [BulkOperationController::class, 'bulkAssignCategory'])->name('bulk.assign-category');
    Route::post('bulk/assign-classification', [BulkOperationController::class, 'bulkAssignClassification'])->name('bulk.assign-classification');
    Route::post('bulk/delete', [BulkOperationController::class, 'bulkDelete'])->name('bulk.delete');
    Route::post('bulk/export', [BulkOperationController::class, 'bulkExport'])->name('bulk.export');
    Route::post('bulk/move-storage', [BulkOperationController::class, 'bulkMoveStorage'])->name('bulk.move-storage');

    // Master data routes - Admin only
    Route::resource('categories', CategoryController::class);
    Route::resource('classifications', ClassificationController::class);

    // Role Management - Admin only
    Route::get('roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
    Route::get('roles/{role}/users', [App\Http\Controllers\Admin\RoleController::class, 'getRoleUsers'])->name('roles.users');
    Route::get('roles/user/{user}/roles', [App\Http\Controllers\Admin\RoleController::class, 'getUserRoles'])->name('roles.user-roles');
    Route::put('roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('roles/assign-user', [App\Http\Controllers\Admin\RoleController::class, 'assignUser'])->name('roles.assign-user');
    Route::post('roles/remove-user', [App\Http\Controllers\Admin\RoleController::class, 'removeUser'])->name('roles.remove-user');
    Route::post('roles/remove-user-roles', [App\Http\Controllers\Admin\RoleController::class, 'removeUserRoles'])->name('roles.remove-user-roles');
    Route::post('roles/bulk-destroy', [App\Http\Controllers\Admin\RoleController::class, 'bulkDestroy'])->name('roles.bulk-destroy');
    Route::post('roles/bulk-remove-users', [App\Http\Controllers\Admin\RoleController::class, 'bulkRemoveUsers'])->name('roles.bulk-remove-users');

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

    // Storage routes
    Route::get('storage', [App\Http\Controllers\StorageLocationController::class, 'index'])->name('storage.index');
    Route::get('storage/create/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'create'])->name('storage.create');
    Route::post('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'store'])->name('storage.store');
    Route::get('storage/edit/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'editLocation'])->name('storage.edit');
    Route::put('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'updateLocation'])->name('storage.update');

    // Generate Labels - New Controller
    Route::get('generate-labels', [App\Http\Controllers\GenerateLabelController::class, 'index'])->name('generate-labels.index');
    Route::post('generate-labels/generate', [App\Http\Controllers\GenerateLabelController::class, 'generate'])->name('generate-labels.generate');
    Route::get('generate-labels/boxes/{rackId}', [App\Http\Controllers\GenerateLabelController::class, 'getBoxes'])->name('generate-labels.boxes');
    Route::get('generate-labels/preview/{rackId}/{boxStart}/{boxEnd}', [App\Http\Controllers\GenerateLabelController::class, 'preview'])->name('generate-labels.preview');

    // Storage Management routes
    Route::resource('storage-management', App\Http\Controllers\StorageManagementController::class)->parameters([
        'storage-management' => 'rack'
    ]);
    Route::get('storage-management/{rack}/grid-data', [App\Http\Controllers\StorageManagementController::class, 'getGridData'])->name('storage-management.grid-data');
    Route::post('storage-management/sync-counts', [App\Http\Controllers\StorageManagementController::class, 'syncCounts'])->name('storage-management.sync-counts');
    Route::post('storage-management/update-box-status', [App\Http\Controllers\StorageManagementController::class, 'updateBoxStatus'])->name('storage-management.update-box-status');

    // Storage AJAX routes
    Route::get('archives/api/rack-rows/{rackId}', [ArchiveController::class, 'getRackRows'])->name('archives.get-rack-rows');
    Route::get('archives/api/rack-row-boxes/{rackId}/{rowNumber}', [ArchiveController::class, 'getRackRowBoxes'])->name('archives.get-rack-row-boxes');
    Route::post('storage/get-boxes', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRack'])->name('storage.get-boxes');
    Route::get('storage/box-contents/{rackId}/{boxNumber}', [App\Http\Controllers\StorageLocationController::class, 'getBoxContents'])->name('storage.box-contents');
    Route::get('storage/suggested-file-number/{rackId}/{boxNumber}', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.suggested-file-number');

    // Bulk Location API routes
    Route::get('storage/get-rack-rows', [App\Http\Controllers\StorageLocationController::class, 'getRackRowsForBulk'])->name('storage.get-rack-rows');
    Route::get('storage/get-boxes-for-rack', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRackBulk'])->name('storage.get-boxes-for-rack');
    Route::get('storage/get-boxes-for-rack-row', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRackRowBulk'])->name('storage.get-boxes-for-rack-row');

    // Generate Box Labels (Legacy - will be replaced)
    Route::get('storage/generate-box-labels', [App\Http\Controllers\StorageLocationController::class, 'generateBoxLabelsForm'])->name('storage.generate-box-labels');
    Route::post('storage/generate-box-labels', [App\Http\Controllers\StorageLocationController::class, 'generateBoxLabels'])->name('storage.generate-box-labels.process');

    // --- CUSTOM ROUTES FIRST ---
    Route::post('storage/generate-box-labels', [App\Http\Controllers\StorageLocationController::class, 'generateBoxLabels'])->name('storage.generate-box-labels.process');
    Route::post('storage/generate-box-file-numbers', [App\Http\Controllers\StorageLocationController::class, 'generateBoxFileNumbers'])->name('storage.generate-box-file-numbers.process');
    Route::get('storage/get-boxes', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRack'])->name('storage.get-boxes');
    // --- END CUSTOM ROUTES ---

    Route::post('storage/{archive}', [App\Http\Controllers\StorageLocationController::class, 'store'])->name('storage.store');
    Route::get('storage/box/{boxNumber}/contents', [App\Http\Controllers\StorageLocationController::class, 'getBoxContents'])->name('storage.box.contents');
    Route::get('storage/box/{boxNumber}/contents', [App\Http\Controllers\StorageLocationController::class, 'getBoxContents'])->name('admin.storage.box.contents');
    Route::get('storage/get-racks', [App\Http\Controllers\StorageLocationController::class, 'getRacks'])->name('admin.storage.get-racks');
    Route::get('storage/box/{rackId}/{boxNumber}/next-file', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.box.next-file');


    // Location filter API routes
    Route::get('archives/api/rack-rows/{rackId}', [ArchiveController::class, 'getRackRows'])->name('archives.get-rack-rows');
    Route::get('archives/api/rack-row-boxes/{rackId}/{rowNumber}', [ArchiveController::class, 'getRackRowBoxes'])->name('archives.get-rack-row-boxes');

    // Edit Storage Location
    Route::get('archives/{archive}/edit-location', [App\Http\Controllers\ArchiveController::class, 'editLocation'])->name('archives.edit-location');
    Route::post('archives/{archive}/update-location', [App\Http\Controllers\ArchiveController::class, 'updateLocation'])->name('archives.update-location');

    // Automatic Box and File Number Generation
    Route::get('storage/generate-box-file-numbers', [App\Http\Controllers\StorageLocationController::class, 'generateBoxFileNumbersForm'])->name('storage.generate-box-file-numbers');
    Route::post('storage/generate-box-file-numbers', [App\Http\Controllers\StorageLocationController::class, 'generateBoxFileNumbers'])->name('storage.generate-box-file-numbers.process');

    // Box Labels Generation
    Route::get('storage/generate-box-labels', [App\Http\Controllers\StorageLocationController::class, 'generateBoxLabelsForm'])->name('storage.generate-box-labels');
    Route::post('storage/generate-box-labels', [App\Http\Controllers\StorageLocationController::class, 'generateBoxLabels'])->name('storage.generate-box-labels.process');
    Route::get('storage/get-boxes', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRack'])->name('storage.get-boxes');



    // Re-evaluation Archives Management
    Route::get('re-evaluation', [App\Http\Controllers\ReEvaluationController::class, 'index'])->name('re-evaluation.index');
    Route::get('re-evaluation/evaluated', [App\Http\Controllers\ReEvaluationController::class, 'evaluated'])->name('re-evaluation.evaluated');
    Route::get('re-evaluation/{archive}', [App\Http\Controllers\ReEvaluationController::class, 'show'])->name('re-evaluation.show');
    Route::post('re-evaluation/{archive}/status', [App\Http\Controllers\ReEvaluationController::class, 'updateStatus'])->name('re-evaluation.update-status');
    Route::post('re-evaluation/bulk-update', [App\Http\Controllers\ReEvaluationController::class, 'bulkUpdateStatus'])->name('re-evaluation.bulk-update');
    Route::post('re-evaluation/export', [App\Http\Controllers\ReEvaluationController::class, 'export'])->name('re-evaluation.export');
    Route::get('re-evaluation/get-archives', [App\Http\Controllers\ReEvaluationController::class, 'getReEvaluationArchives'])->name('re-evaluation.get-archives');
});

// ========================================
// STAFF ROUTES
// ========================================
Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Analytics access for staff
    Route::get('analytics', [App\Http\Controllers\Staff\AnalyticsController::class, 'index'])->name('analytics.index');

    // Archives - CRUD (no delete)
    Route::get('archives', [App\Http\Controllers\Staff\ArchiveController::class, 'index'])->name('archives.index');
    Route::get('archives/aktif', [App\Http\Controllers\Staff\ArchiveController::class, 'aktif'])->name('archives.aktif');
    Route::get('archives/inaktif', [App\Http\Controllers\Staff\ArchiveController::class, 'inaktif'])->name('archives.inaktif');
    Route::get('archives/permanen', [App\Http\Controllers\Staff\ArchiveController::class, 'permanen'])->name('archives.permanen');
    Route::get('archives/musnah', [App\Http\Controllers\Staff\ArchiveController::class, 'musnah'])->name('archives.musnah');

    Route::get('archives/parent', [App\Http\Controllers\Staff\ArchiveController::class, 'parentArchives'])->name('archives.parent');

    Route::get('archives/create', [App\Http\Controllers\Staff\ArchiveController::class, 'create'])->name('archives.create');
    Route::post('archives', [App\Http\Controllers\Staff\ArchiveController::class, 'store'])->name('archives.store');
    Route::get('archives/{archive}', [App\Http\Controllers\Staff\ArchiveController::class, 'show'])->name('archives.show');
    Route::get('archives/{archive}/edit', [App\Http\Controllers\Staff\ArchiveController::class, 'edit'])->name('archives.edit');
    Route::put('archives/{archive}', [App\Http\Controllers\Staff\ArchiveController::class, 'update'])->name('archives.update');
    Route::delete('archives/{archive}', [App\Http\Controllers\Staff\ArchiveController::class, 'destroy'])->name('archives.destroy');

    // Edit Storage Location
    Route::get('archives/{archive}/edit-location', [App\Http\Controllers\Staff\ArchiveController::class, 'editLocation'])->name('archives.edit-location');
    Route::post('archives/{archive}/update-location', [App\Http\Controllers\Staff\ArchiveController::class, 'updateLocation'])->name('archives.update-location');


    // Personal Files routes
    Route::get('personal-files', [StaffPersonalFilesController::class, 'index'])->name('personal-files.index');

    // Archive AJAX routes
    Route::get('archives/api/classification-details/{classification}', [ArchiveController::class, 'getClassificationDetails'])->name('archives.get-classification-details');
    Route::get('archives/api/classifications-by-category', [ArchiveController::class, 'getClassificationsByCategory'])->name('archives.get-classifications-by-category');
    Route::post('archives/change-status', [ArchiveController::class, 'changeStatus'])->name('archives.change-status');

    // Export routes
    Route::get('archives/export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('archives.export-form');
    Route::post('archives/export', [ArchiveController::class, 'export'])->name('archives.export.process');

    // Search routes for staff
    Route::get('search', [App\Http\Controllers\Staff\SearchController::class, 'index'])->name('search.index');
    Route::get('search/results', [App\Http\Controllers\Staff\SearchController::class, 'search'])->name('search.search');
    Route::get('search/autocomplete', [App\Http\Controllers\Staff\SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::get('search/export', [App\Http\Controllers\Staff\SearchController::class, 'exportResults'])->name('search.export');

    // Export Excel menu for staff
    Route::get('export', [ArchiveController::class, 'exportMenu'])->name('export.index');
    Route::get('export-form/{status?}', [ArchiveController::class, 'exportForm'])->name('export-form');
    Route::post('export', [ArchiveController::class, 'export'])->name('export.process');

    // Generate Box Labels for staff
    Route::get('storage/generate-box-labels', [App\Http\Controllers\GenerateLabelController::class, 'index'])->name('storage.generate-box-labels');
    Route::post('storage/generate', [App\Http\Controllers\GenerateLabelController::class, 'generate'])->name('storage.generate');
    Route::get('storage/preview', [App\Http\Controllers\GenerateLabelController::class, 'preview'])->name('storage.preview');

    // Export routes for staff
    Route::get('export', [App\Http\Controllers\ArchiveController::class, 'exportMenu'])->name('export.index');
    Route::post('export', [App\Http\Controllers\ArchiveController::class, 'export'])->name('export.process');
    Route::get('export-form/{status?}', [App\Http\Controllers\ArchiveController::class, 'exportForm'])->name('export-form');

    // Generate Labels routes for staff
    Route::get('generate-labels', [App\Http\Controllers\GenerateLabelController::class, 'index'])->name('generate-labels.index');
    Route::post('generate-labels/generate', [App\Http\Controllers\GenerateLabelController::class, 'generate'])->name('generate-labels.generate');
    Route::get('generate-labels/preview', [App\Http\Controllers\GenerateLabelController::class, 'preview'])->name('generate-labels.preview');
    Route::get('generate-labels/boxes/{rackId}', [App\Http\Controllers\GenerateLabelController::class, 'getBoxes'])->name('generate-labels.boxes');
    Route::get('generate-labels/preview/{rackId}/{boxStart}/{boxEnd}', [App\Http\Controllers\GenerateLabelController::class, 'preview'])->name('generate-labels.preview-data');

    // Storage Management routes for staff
    Route::resource('storage-management', App\Http\Controllers\StorageManagementController::class)->parameters([
        'storage-management' => 'rack'
    ]);
    Route::get('storage-management/{rack}/grid-data', [App\Http\Controllers\StorageManagementController::class, 'getGridData'])->name('storage-management.grid-data');

    // Reports routes for staff
    Route::get('reports/retention-dashboard', [ReportController::class, 'retentionDashboard'])->name('reports.retention-dashboard');

    // Related Archives routes for staff
    Route::get('archives/related/category', [RelatedArchivesController::class, 'byCategory'])->name('archives.related-category');
    Route::get('archives/{archive}/related', [RelatedArchivesController::class, 'index'])->name('archives.related');
    Route::get('archives/{parentArchive}/create-related', [RelatedArchivesController::class, 'createRelated'])->name('archives.create-related');
    Route::post('archives/{parentArchive}/store-related', [RelatedArchivesController::class, 'storeRelated'])->name('archives.store-related');
    Route::post('archives/bulk-update-location', [RelatedArchivesController::class, 'bulkUpdateLocation'])->name('archives.bulk-update-location');
    Route::get('archives/storage-management/{rack}/grid-data', [App\Http\Controllers\StorageManagementController::class, 'getGridData'])->name('archives.storage-management.grid-data');

    // Bulk Operations for staff
    Route::get('bulk', [BulkOperationController::class, 'index'])->name('bulk.index');
    Route::get('bulk/get-archives', [BulkOperationController::class, 'getArchives'])->name('bulk.get-archives');
    Route::post('bulk/status-change', [BulkOperationController::class, 'bulkStatusChange'])->name('bulk.status-change');
    Route::post('bulk/assign-category', [BulkOperationController::class, 'bulkAssignCategory'])->name('bulk.assign-category');
    Route::post('bulk/assign-classification', [BulkOperationController::class, 'bulkAssignClassification'])->name('bulk.assign-classification');
    Route::post('bulk/export', [BulkOperationController::class, 'bulkExport'])->name('bulk.export');
    Route::post('bulk/move-storage', [BulkOperationController::class, 'bulkMoveStorage'])->name('bulk.move-storage');

    // Storage routes for staff
    Route::get('storage', [App\Http\Controllers\StorageLocationController::class, 'index'])->name('storage.index');
    Route::get('storage/create/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'create'])->name('storage.create');
    Route::post('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'store'])->name('storage.store');
    Route::get('storage/edit/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'editLocation'])->name('storage.edit');
    Route::put('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'updateLocation'])->name('storage.update');

    // Storage AJAX routes for staff
    Route::get('archives/api/rack-rows/{rackId}', [App\Http\Controllers\Staff\ArchiveController::class, 'getRackRows'])->name('archives.get-rack-rows');
    Route::get('archives/api/rack-row-boxes/{rackId}/{rowNumber}', [App\Http\Controllers\Staff\ArchiveController::class, 'getRackRowBoxes'])->name('archives.get-rack-row-boxes');
    Route::post('storage/get-boxes', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRack'])->name('storage.get-boxes');
    Route::get('storage/box-contents/{rackId}/{boxNumber}', [App\Http\Controllers\StorageLocationController::class, 'getBoxContents'])->name('storage.box-contents');
    Route::get('storage/suggested-file-number/{rackId}/{boxNumber}', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.suggested-file-number');
    Route::get('storage/get-rack-rows', [App\Http\Controllers\StorageLocationController::class, 'getRackRowsForBulk'])->name('storage.get-rack-rows');
    Route::get('storage/get-boxes-for-rack', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRackBulk'])->name('storage.get-boxes-for-rack');
    Route::get('storage/get-boxes-for-rack-row', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRackRowBulk'])->name('storage.get-boxes-for-rack-row');
    Route::get('storage/box/{rackId}/{boxNumber}/next-file', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.box.next-file');

    // Storage Management AJAX routes for staff
    Route::post('storage-management/sync-counts', [App\Http\Controllers\StorageManagementController::class, 'syncCounts'])->name('storage-management.sync-counts');
    Route::post('storage-management/update-box-status', [App\Http\Controllers\StorageManagementController::class, 'updateBoxStatus'])->name('storage-management.update-box-status');

    // Bulk Operations for staff
    Route::get('bulk', [BulkOperationController::class, 'index'])->name('bulk.index');
    Route::get('bulk/get-archives', [BulkOperationController::class, 'getArchives'])->name('bulk.get-archives');
    Route::post('bulk/status-change', [BulkOperationController::class, 'bulkStatusChange'])->name('bulk.status-change');
    Route::post('bulk/assign-classification', [BulkOperationController::class, 'bulkAssignClassification'])->name('bulk.assign-classification');
    Route::post('bulk/export', [BulkOperationController::class, 'bulkExport'])->name('bulk.export');
    Route::post('bulk/delete', [BulkOperationController::class, 'bulkDelete'])->name('bulk.delete');
    Route::post('bulk/move-storage', [BulkOperationController::class, 'bulkMoveStorage'])->name('bulk.move-storage');

    // Storage Location Management for staff
    Route::get('storage', [App\Http\Controllers\StorageLocationController::class, 'index'])->name('storage.index');
    Route::get('storage/create/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'create'])->name('storage.create');
    Route::post('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'store'])->name('storage.store');
    Route::get('storage/edit/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'editLocation'])->name('storage.edit');
    Route::put('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'updateLocation'])->name('storage.update');
    Route::get('storage/box/{rackId}/{boxNumber}/contents', [App\Http\Controllers\StorageLocationController::class, 'getBoxContents'])->name('storage.box.contents');
    Route::get('storage/box/{rackId}/{boxNumber}/next-file', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.box.next-file');
    Route::get('storage/rack-row/boxes', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRackRow'])->name('storage.rack-row.boxes');
    Route::get('storage/rack/rows', [App\Http\Controllers\StorageLocationController::class, 'getRackRows'])->name('storage.rack.rows');
    Route::get('storage/generate-box-labels', [App\Http\Controllers\GenerateLabelController::class, 'index'])->name('storage.generate-box-labels');
    Route::post('storage/generate', [App\Http\Controllers\GenerateLabelController::class, 'generate'])->name('storage.generate');
    Route::get('storage/preview', [App\Http\Controllers\GenerateLabelController::class, 'preview'])->name('storage.preview');

    // Re-evaluation Archives Management for staff
    Route::get('re-evaluation', [App\Http\Controllers\ReEvaluationController::class, 'index'])->name('re-evaluation.index');
    Route::get('re-evaluation/evaluated', [App\Http\Controllers\ReEvaluationController::class, 'evaluated'])->name('re-evaluation.evaluated');
    Route::get('re-evaluation/{archive}', [App\Http\Controllers\ReEvaluationController::class, 'show'])->name('re-evaluation.show');
    Route::post('re-evaluation/{archive}/status', [App\Http\Controllers\ReEvaluationController::class, 'updateStatus'])->name('re-evaluation.update-status');
    Route::post('re-evaluation/bulk-update', [App\Http\Controllers\ReEvaluationController::class, 'bulkUpdateStatus'])->name('re-evaluation.bulk-update');
    Route::post('re-evaluation/export', [App\Http\Controllers\ReEvaluationController::class, 'export'])->name('re-evaluation.export');
    Route::get('re-evaluation/get-archives', [App\Http\Controllers\ReEvaluationController::class, 'getReEvaluationArchives'])->name('re-evaluation.get-archives');


});

// ========================================
// INTERN ROUTES
// ========================================
Route::middleware(['auth', 'verified', 'role:intern'])->prefix('intern')->name('intern.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [InternDashboardController::class, 'index'])->name('dashboard');

    // Archives - View, Create, and Edit (no delete)
    Route::get('archives', [App\Http\Controllers\Intern\ArchiveController::class, 'index'])->name('archives.index');
    Route::get('archives/aktif', [App\Http\Controllers\Intern\ArchiveController::class, 'aktif'])->name('archives.aktif');
    Route::get('archives/inaktif', [App\Http\Controllers\Intern\ArchiveController::class, 'inaktif'])->name('archives.inaktif');
    Route::get('archives/permanen', [App\Http\Controllers\Intern\ArchiveController::class, 'permanen'])->name('archives.permanen');
    Route::get('archives/musnah', [App\Http\Controllers\Intern\ArchiveController::class, 'musnah'])->name('archives.musnah');
    Route::get('archives/parent', [App\Http\Controllers\Intern\ArchiveController::class, 'parentArchives'])->name('archives.parent');
    Route::get('archives/create', [App\Http\Controllers\Intern\ArchiveController::class, 'create'])->name('archives.create');
    Route::post('archives', [App\Http\Controllers\Intern\ArchiveController::class, 'store'])->name('archives.store');
    Route::get('archives/{archive}', [App\Http\Controllers\Intern\ArchiveController::class, 'show'])->name('archives.show');
    Route::get('archives/{archive}/edit', [App\Http\Controllers\Intern\ArchiveController::class, 'edit'])->name('archives.edit');
    Route::put('archives/{archive}', [App\Http\Controllers\Intern\ArchiveController::class, 'update'])->name('archives.update');

    // Edit Storage Location
    Route::get('archives/{archive}/edit-location', [App\Http\Controllers\Intern\ArchiveController::class, 'editLocation'])->name('archives.edit-location');
    Route::post('archives/{archive}/update-location', [App\Http\Controllers\Intern\ArchiveController::class, 'updateLocation'])->name('archives.update-location');

    // Intern cannot delete archives

    // Archive AJAX routes
    Route::get('archives/api/classification-details/{classification}', [App\Http\Controllers\Intern\ArchiveController::class, 'getClassificationDetails'])->name('archives.get-classification-details');
    Route::get('archives/api/classifications-by-category', [App\Http\Controllers\Intern\ArchiveController::class, 'getClassificationsByCategory'])->name('archives.get-classifications-by-category');
    Route::post('archives/change-status', [App\Http\Controllers\Intern\ArchiveController::class, 'changeStatus'])->name('archives.change-status');

    // Export routes (view only)
    Route::get('archives/export/{status?}', [App\Http\Controllers\Intern\ArchiveController::class, 'exportArchives'])->name('archives.export');
    Route::get('archives/export-form/{status?}', [App\Http\Controllers\Intern\ArchiveController::class, 'exportForm'])->name('archives.export-form');
    Route::post('archives/export', [App\Http\Controllers\Intern\ArchiveController::class, 'export'])->name('archives.export.process');

    // Search routes for intern
    Route::get('search', [App\Http\Controllers\Intern\SearchController::class, 'index'])->name('search.index');
    Route::get('search/results', [App\Http\Controllers\Intern\SearchController::class, 'search'])->name('search.search');
    Route::get('search/autocomplete', [App\Http\Controllers\Intern\SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::get('search/export', [App\Http\Controllers\Intern\SearchController::class, 'exportResults'])->name('search.export');

    // Export Excel menu for intern
    Route::get('export', [App\Http\Controllers\Intern\ArchiveController::class, 'exportMenu'])->name('export.index');
    Route::get('export-form/{status?}', [App\Http\Controllers\Intern\ArchiveController::class, 'exportForm'])->name('export-form');
    Route::post('export', [App\Http\Controllers\Intern\ArchiveController::class, 'export'])->name('export.process');

    // Generate Box Labels for intern
    Route::get('generate-labels', [App\Http\Controllers\GenerateLabelController::class, 'index'])->name('generate-labels.index');
    Route::post('generate-labels/generate', [App\Http\Controllers\GenerateLabelController::class, 'generate'])->name('generate-labels.generate');
    Route::get('generate-labels/boxes/{rackId}', [App\Http\Controllers\GenerateLabelController::class, 'getBoxes'])->name('generate-labels.boxes');
    Route::get('generate-labels/preview', [App\Http\Controllers\GenerateLabelController::class, 'preview'])->name('generate-labels.preview');
    Route::get('generate-labels/preview/{rackId}/{boxStart}/{boxEnd}', [App\Http\Controllers\GenerateLabelController::class, 'preview'])->name('generate-labels.preview-data');

    // Storage Management routes for intern
    Route::resource('storage-management', App\Http\Controllers\StorageManagementController::class)->parameters([
        'storage-management' => 'rack'
    ]);
    Route::get('storage-management/{rack}/grid-data', [App\Http\Controllers\StorageManagementController::class, 'getGridData'])->name('storage-management.grid-data');

    // Related Archives routes for intern
    Route::get('archives/related/category', [RelatedArchivesController::class, 'byCategory'])->name('archives.related-category');
    Route::get('archives/{archive}/related', [RelatedArchivesController::class, 'index'])->name('archives.related');
    Route::get('archives/{parentArchive}/create-related', [RelatedArchivesController::class, 'createRelated'])->name('archives.create-related');
    Route::post('archives/{parentArchive}/store-related', [RelatedArchivesController::class, 'storeRelated'])->name('archives.store-related');
    Route::post('archives/bulk-update-location', [RelatedArchivesController::class, 'bulkUpdateLocation'])->name('archives.bulk-update-location');
    Route::get('archives/storage-management/{rack}/grid-data', [App\Http\Controllers\StorageManagementController::class, 'getGridData'])->name('archives.storage-management.grid-data');

    // Bulk Operations for intern
    Route::get('bulk', [BulkOperationController::class, 'index'])->name('bulk.index');
    Route::get('bulk/get-archives', [BulkOperationController::class, 'getArchives'])->name('bulk.get-archives');
    Route::post('bulk/status-change', [BulkOperationController::class, 'bulkStatusChange'])->name('bulk.status-change');
    Route::post('bulk/assign-category', [BulkOperationController::class, 'bulkAssignCategory'])->name('bulk.assign-category');
    Route::post('bulk/assign-classification', [BulkOperationController::class, 'bulkAssignClassification'])->name('bulk.assign-classification');
    Route::post('bulk/export', [BulkOperationController::class, 'bulkExport'])->name('bulk.export');
    Route::post('bulk/move-storage', [BulkOperationController::class, 'bulkMoveStorage'])->name('bulk.move-storage');

    // Storage routes for intern
    Route::get('storage', [App\Http\Controllers\StorageLocationController::class, 'index'])->name('storage.index');
    Route::get('storage/create/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'create'])->name('storage.create');
    Route::post('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'store'])->name('storage.store');
    Route::get('storage/edit/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'editLocation'])->name('storage.edit');
    Route::put('storage/{archiveId}', [App\Http\Controllers\StorageLocationController::class, 'updateLocation'])->name('storage.update');

    // Storage AJAX routes for intern
    Route::get('archives/api/rack-rows/{rackId}', [App\Http\Controllers\Intern\ArchiveController::class, 'getRackRows'])->name('archives.get-rack-rows');
    Route::get('archives/api/rack-row-boxes/{rackId}/{rowNumber}', [App\Http\Controllers\Intern\ArchiveController::class, 'getRackRowBoxes'])->name('archives.get-rack-row-boxes');
    Route::post('storage/get-boxes', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRack'])->name('storage.get-boxes');
    Route::get('storage/box-contents/{rackId}/{boxNumber}', [App\Http\Controllers\StorageLocationController::class, 'getBoxContents'])->name('storage.box-contents');
    Route::get('storage/suggested-file-number/{rackId}/{boxNumber}', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.suggested-file-number');
    Route::get('storage/get-rack-rows', [App\Http\Controllers\StorageLocationController::class, 'getRackRowsForBulk'])->name('storage.get-rack-rows');
    Route::get('storage/get-boxes-for-rack', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRackBulk'])->name('storage.get-boxes-for-rack');
    Route::get('storage/get-boxes-for-rack-row', [App\Http\Controllers\StorageLocationController::class, 'getBoxesForRackRowBulk'])->name('storage.get-boxes-for-rack-row');
    Route::get('storage/box/{rackId}/{boxNumber}/next-file', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.box.next-file');

    // Storage Management AJAX routes for intern
    Route::post('storage-management/sync-counts', [App\Http\Controllers\StorageManagementController::class, 'syncCounts'])->name('storage-management.sync-counts');
    Route::post('storage-management/update-box-status', [App\Http\Controllers\StorageManagementController::class, 'updateBoxStatus'])->name('storage-management.update-box-status');

    // Reports routes for intern (view only)
    Route::get('reports/retention-dashboard', [ReportController::class, 'retentionDashboard'])->name('reports.retention-dashboard');

    // Storage Location Management for intern
    Route::get('storage', [App\Http\Controllers\StorageLocationController::class, 'index'])->name('storage.index');
    Route::get('storage/{archive}/create', [App\Http\Controllers\StorageLocationController::class, 'create'])->name('storage.create');
    Route::post('storage/{archive}', [App\Http\Controllers\StorageLocationController::class, 'store'])->name('storage.store');
    Route::get('storage/box/{rackId}/{boxNumber}/contents', [App\Http\Controllers\StorageLocationController::class, 'getBoxContents'])->name('storage.box.contents');
    Route::get('storage/box/{rackId}/{boxNumber}/next-file', [App\Http\Controllers\StorageLocationController::class, 'getSuggestedFileNumber'])->name('storage.box.next-file');

    // Re-evaluation Archives Management for intern (view only)
    Route::get('re-evaluation', [App\Http\Controllers\ReEvaluationController::class, 'index'])->name('re-evaluation.index');
    Route::get('re-evaluation/evaluated', [App\Http\Controllers\ReEvaluationController::class, 'evaluated'])->name('re-evaluation.evaluated');
    Route::get('re-evaluation/{archive}', [App\Http\Controllers\ReEvaluationController::class, 'show'])->name('re-evaluation.show');
    Route::post('re-evaluation/{archive}/status', [App\Http\Controllers\ReEvaluationController::class, 'updateStatus'])->name('re-evaluation.update-status');
    Route::post('re-evaluation/bulk-update', [App\Http\Controllers\ReEvaluationController::class, 'bulkUpdateStatus'])->name('re-evaluation.bulk-update');
    Route::get('re-evaluation/get-archives', [App\Http\Controllers\ReEvaluationController::class, 'getReEvaluationArchives'])->name('re-evaluation.get-archives');
});

// ========================================
// AUTH ROUTES
// ========================================
require __DIR__.'/auth.php';

// ========================================
// DEBUG ROUTE
// ========================================
Route::get('/debug-info', function () {
    return view('debug-info');
})->name('debug.info');
