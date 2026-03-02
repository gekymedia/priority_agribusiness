<?php

namespace App\Services\Notifications\Sms;

interface SmsDriverInterface
{
    /**
     * Send an SMS message.
     *
     * @param string $to Phone number in E.164 format (e.g., +233241234567)
     * @param string $message Message content
     * @return array ['success' => bool, 'message' => string, ...]
     */
    public function send(string $to, string $message): array;
}
