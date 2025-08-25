<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('Telegram webhook received', ['data' => $data]);

            $this->telegramService->handleWebhook($data);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error processing Telegram webhook', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function test()
    {
        try {
            $chatId = config('services.telegram.test_chat_id');

            if (!$chatId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Test chat ID not configured'
                ], 400);
            }

            $result = $this->telegramService->sendMessage(
                $chatId,
                "🧪 <b>Test Message dari ARSIPIN</b>\n\nBot Telegram berfungsi dengan baik!\nWaktu: " . now()->format('d M Y H:i') . " WIB"
            );

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Test message sent successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send test message'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error sending test message', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function setWebhook(Request $request)
    {
        try {
            $url = $request->input('url');

            if (!$url) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'URL is required'
                ], 400);
            }

            $botToken = config('services.telegram.bot_token');
            $webhookUrl = "https://api.telegram.org/bot{$botToken}/setWebhook";

            $response = \Illuminate\Support\Facades\Http::post($webhookUrl, [
                'url' => $url . '/api/telegram/webhook'
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['ok']) {
                    Log::info('Telegram webhook set successfully', ['url' => $url]);
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Webhook set successfully',
                        'data' => $result
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to set webhook: ' . ($result['description'] ?? 'Unknown error')
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to communicate with Telegram API'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error setting Telegram webhook', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteWebhook()
    {
        try {
            $botToken = config('services.telegram.bot_token');
            $webhookUrl = "https://api.telegram.org/bot{$botToken}/deleteWebhook";

            $response = \Illuminate\Support\Facades\Http::post($webhookUrl);

            if ($response->successful()) {
                $result = $response->json();

                if ($result['ok']) {
                    Log::info('Telegram webhook deleted successfully');
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Webhook deleted successfully',
                        'data' => $result
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to delete webhook: ' . ($result['description'] ?? 'Unknown error')
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to communicate with Telegram API'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting Telegram webhook', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendTestMessage(Request $request)
    {
        try {
            $chatId = $request->input('chat_id');
            $message = $request->input('message', 'Test message dari ARSIPIN');

            if (!$chatId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat ID is required'
                ], 400);
            }

            $result = $this->telegramService->sendMessage(
                $chatId,
                "🧪 <b>Test Message</b>\n\n{$message}\n\nWaktu: " . now()->format('d M Y H:i') . " WIB"
            );

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Test message sent successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send test message'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error sending test message', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendWelcome(Request $request)
    {
        try {
            $chatId = $request->input('chat_id');

            if (!$chatId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat ID is required'
                ], 400);
            }

            $result = $this->telegramService->sendWelcomeMessage($chatId, ['first_name' => 'User']);

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Welcome message sent successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send welcome message'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error sending welcome message', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
