<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArchiveController;
use App\Http\Controllers\Api\StorageController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Authentication routes
Route::post('/auth/login-test', [AuthController::class, 'loginTest'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Telegram Bot Routes
Route::prefix('telegram')->group(function () {
    // Webhook untuk menerima pesan dari Telegram
    Route::post('/webhook', [App\Http\Controllers\Api\TelegramController::class, 'webhook'])
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

    // Test dan management routes - PROTECTED with admin middleware
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/test', [App\Http\Controllers\Api\TelegramController::class, 'test']);
        Route::post('/set-webhook', [App\Http\Controllers\Api\TelegramController::class, 'setWebhook']);
        Route::delete('/delete-webhook', [App\Http\Controllers\Api\TelegramController::class, 'deleteWebhook']);
        Route::post('/send-test', [App\Http\Controllers\Api\TelegramController::class, 'sendTestMessage']);
        Route::post('/send-welcome', [App\Http\Controllers\Api\TelegramController::class, 'sendWelcome']);
    });
});

// API routes with authentication and rate limiting
Route::middleware(['auth:sanctum', 'rate.limit:100,1'])->group(function () {
    // User management
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Archive endpoints
    Route::prefix('archives')->group(function () {
        Route::get('/', [ArchiveController::class, 'index']);
        Route::get('/{archive}', [ArchiveController::class, 'show']);
        Route::get('/status/{status}', [ArchiveController::class, 'getByStatus']);
    });

    // Storage endpoints
    Route::prefix('storage')->group(function () {
        Route::get('/racks', [StorageController::class, 'getRacks']);
        Route::get('/racks/{rack}', [StorageController::class, 'getRack']);
        Route::get('/boxes', [StorageController::class, 'getBoxes']);
        Route::get('/boxes/{box}', [StorageController::class, 'getBox']);
    });

    // Report endpoints
    Route::prefix('reports')->group(function () {
        Route::get('/summary', [ReportController::class, 'summary']);
        Route::get('/retention', [ReportController::class, 'retention']);
        Route::get('/storage-utilization', [ReportController::class, 'storageUtilization']);
    });
});
