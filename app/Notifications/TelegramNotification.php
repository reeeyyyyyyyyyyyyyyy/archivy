<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TelegramNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $chatId;

    public function __construct($message, $chatId = null)
    {
        $this->message = $message;
        $this->chatId = $chatId ?? config('services.telegram.chat_id');
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        return [
            'chat_id' => $this->chatId,
            'text' => $this->message,
            'parse_mode' => 'HTML'
        ];
    }
}
