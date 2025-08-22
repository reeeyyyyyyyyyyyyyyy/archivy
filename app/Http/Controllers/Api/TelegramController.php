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

    /**
     * Handle webhook dari Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('Telegram webhook received', $data);

            $this->telegramService->handleWebhook($data);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Test koneksi bot
     */
    public function test()
    {
        try {
            $result = $this->telegramService->testConnection();

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bot connected successfully!',
                    'bot_name' => $result['bot_name'],
                    'username' => $result['username']
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set webhook URL
     */
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

            $result = $this->telegramService->setWebhook($url);

            if ($result && isset($result['ok']) && $result['ok']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook set successfully!',
                    'url' => $url
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to set webhook'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        try {
            $result = $this->telegramService->deleteWebhook();

            if ($result && isset($result['ok']) && $result['ok']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook deleted successfully!'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete webhook'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test message
     */
    public function sendTestMessage(Request $request)
    {
        try {
            $chatId = $request->input('chat_id');
            $message = $request->input('message', 'Test message from ARSIPIN Bot!');

            if (!$chatId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat ID is required'
                ], 400);
            }

            $result = $this->telegramService->sendMessage($chatId, $message);

            if ($result && isset($result['ok']) && $result['ok']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Test message sent successfully!'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send test message'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send welcome message with keyboard
     */
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

            $this->telegramService->sendWelcomeMessage($chatId);

            return response()->json([
                'status' => 'success',
                'message' => 'Welcome message sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
