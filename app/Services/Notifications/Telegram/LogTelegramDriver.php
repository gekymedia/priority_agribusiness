<?php

namespace App\Services\Notifications\Telegram;

use Illuminate\Support\Facades\Log;

class LogTelegramDriver implements TelegramDriverInterface
{
    public function send(string $to, string $message): array
    {
        Log::info('[TELEGRAM] Message would be sent', [
            'to' => $to,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'message' => 'Telegram message logged (not actually sent)',
            'to' => $to,
        ];
    }
}
