<?php

namespace App\Services\Notifications\GekyChat;

use Illuminate\Support\Facades\Log;

class LogGekyChatDriver implements GekyChatDriverInterface
{
    public function send(string $to, string $message): array
    {
        Log::info('[GEKYCHAT] Message would be sent', [
            'to' => $to,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'message' => 'GekyChat message logged (not actually sent)',
            'to' => $to,
        ];
    }
}
