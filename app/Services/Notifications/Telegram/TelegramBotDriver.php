<?php

namespace App\Services\Notifications\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotDriver implements TelegramDriverInterface
{
    public function send(string $to, string $message): array
    {
        $botToken = config('notifications.telegram.bot_token');

        if (!$botToken) {
            Log::error('[TELEGRAM] Missing bot token');
            return [
                'success' => false,
                'error' => 'Missing Telegram bot token',
            ];
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $to,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['ok'] ?? false) {
                    Log::info('[TELEGRAM] Message sent successfully', [
                        'to' => $to,
                        'response' => $data,
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Telegram message sent successfully',
                        'response' => $data,
                    ];
                }
            }

            Log::error('[TELEGRAM] Send failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('description', 'Unknown error'),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('[TELEGRAM] Exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
