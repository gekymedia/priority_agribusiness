<?php

namespace App\Services\Notifications\GekyChat;

interface GekyChatDriverInterface
{
    /**
     * Send a GekyChat message.
     *
     * @param string $to User identifier or phone number
     * @param string $message Message content
     * @return array ['success' => bool, 'message' => string, ...]
     */
    public function send(string $to, string $message): array;
}
