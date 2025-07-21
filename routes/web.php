<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/api/classifications', [ClassificationController::class, 'getFilteredClassifications'])->name('api.classifications');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('archives/inaktif', [ArchiveController::class, 'inaktif'])->name('archives.inaktif');
    Route::get('archives/permanen', [ArchiveController::class, 'permanen'])->name('archives.permanen');
    Route::get('archives/musnah', [ArchiveController::class, 'musnah'])->name('archives.musnah');
    Route::resource('archives', ArchiveController::class)->except(['index'])->names('archives');
    Route::get('archives', [ArchiveController::class, 'index'])->name('archives.index');

    Route::resource('categories', CategoryController::class);
    Route::resource('classifications', ClassificationController::class);

    Route::get('/archives/status/aktif', [App\Http\Controllers\ArchiveController::class, 'indexAktif'])->name('archives.status.aktif');
    Route::get('/archives/status/inaktif', [App\Http\Controllers\ArchiveController::class, 'indexInaktif'])->name('archives.status.inaktif');
    Route::get('/archives/status/permanen', [App\Http\Controllers\ArchiveController::class, 'indexPermanen'])->name('archives.status.permanen');
    Route::get('/archives/status/musnah', [App\Http\Controllers\ArchiveController::class, 'indexMusnah'])->name('archives.status.musnah');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('api')->name('api.')->group(function() {
    Route::get('/classifications', [App\Http\Controllers\ArchiveController::class, 'getClassificationsByCategory'])->name('classifications.by_category');
    Route::get('/classifications/{classification}', [App\Http\Controllers\ArchiveController::class, 'getClassificationDetails'])->name('classifications.details');
});


require __DIR__.'/auth.php';
