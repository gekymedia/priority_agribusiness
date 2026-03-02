<?php

namespace App\Services\Notifications\WhatsApp;

use Illuminate\Support\Facades\Log;

class LogWhatsAppDriver implements WhatsAppDriverInterface
{
    public function sendText(string $toE164, string $message): array
    {
        Log::info('[WHATSAPP] Message would be sent', [
            'to' => $toE164,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'message' => 'WhatsApp message logged (not actually sent)',
            'to' => $toE164,
        ];
    }
}
