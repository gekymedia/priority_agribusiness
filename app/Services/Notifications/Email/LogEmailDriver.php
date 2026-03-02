<?php

namespace App\Services\Notifications\Email;

use Illuminate\Support\Facades\Log;

class LogEmailDriver implements EmailDriverInterface
{
    public function send(string $to, string $message, ?string $subject = null): array
    {
        Log::info('[EMAIL] Email would be sent', [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'message' => 'Email logged (not actually sent)',
            'to' => $to,
        ];
    }
}
