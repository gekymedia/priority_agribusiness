<?php

namespace App\Services\Notifications\Sms;

use Illuminate\Support\Facades\Log;

class LogSmsDriver implements SmsDriverInterface
{
    public function send(string $to, string $message): array
    {
        Log::info('[SMS] Message would be sent', [
            'to' => $to,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'message' => 'SMS logged (not actually sent)',
            'to' => $to,
        ];
    }
}
