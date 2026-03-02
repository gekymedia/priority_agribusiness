<?php

namespace App\Services\Notifications\Email;

interface EmailDriverInterface
{
    /**
     * Send an email message.
     *
     * @param string $to Email address
     * @param string $message Message content
     * @param string|null $subject Email subject
     * @return array ['success' => bool, 'message' => string, ...]
     */
    public function send(string $to, string $message, ?string $subject = null): array;
}
