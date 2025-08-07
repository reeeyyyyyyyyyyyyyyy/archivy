<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArchiveController;
use App\Http\Controllers\Api\StorageController;
use App\Http\Controllers\Api\ReportController;
use App\Services\TelegramService;
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

// Simple test endpoint
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!'
    ]);
});

// Test route to check if API is accessible
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'Pong! API is accessible'
    ]);
});

// Authentication routes
Route::post('/auth/login-test', [AuthController::class, 'loginTest'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Telegram Webhook Route
Route::post('/telegram/webhook', function (Request $request) {
    try {
        $update = $request->all();
        $message = $update['message'] ?? null;

        if (!$message) {
            return response()->json(['success' => true]);
        }

        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $messageId = $message['message_id'];

        // Check if this message was already processed
        $processedKey = "telegram_processed_{$messageId}";
        if (Cache::has($processedKey)) {
            return response()->json(['success' => true]);
        }

        // Mark as processed
        Cache::put($processedKey, true, now()->addHours(1));

        $telegramService = app(TelegramService::class);

        // Process the message
        $text = trim($text);

        // Handle search command
        if (preg_match('/^\/cari\s+(.+)$/i', $text, $matches)) {
            $query = trim($matches[1]);
            $telegramService->sendSearchResults($chatId, $query);
            return response()->json(['success' => true]);
        }

        // Handle help command
        if (preg_match('/^\/help$/i', $text)) {
            $telegramService->sendHelpMessage($chatId);
            return response()->json(['success' => true]);
        }

        // Handle status command
        if (preg_match('/^\/status$/i', $text)) {
            $telegramService->sendStatusMessage($chatId);
            return response()->json(['success' => true]);
        }

        // Handle unknown commands
        if (str_starts_with($text, '/')) {
            $telegramService->sendUnknownCommandMessage($chatId);
            return response()->json(['success' => true]);
        }

        // If it's not a command, treat as search query
        if (!empty($text)) {
            $telegramService->sendSearchResults($chatId, $text);
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Telegram webhook error', [
            'error' => $e->getMessage(),
            'request' => $request->all()
        ]);

        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
})->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// API routes with authentication
Route::middleware('auth:sanctum')->group(function () {
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
