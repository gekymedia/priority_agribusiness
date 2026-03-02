<?php

namespace App\Services\Notifications\Telegram;

interface TelegramDriverInterface
{
    /**
     * Send a Telegram message.
     *
     * @param string $to Chat ID or username
     * @param string $message Message content
     * @return array ['success' => bool, 'message' => string, ...]
     */
    public function send(string $to, string $message): array;
}
