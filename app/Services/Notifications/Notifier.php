<?php

namespace App\Services\Notifications;

use App\Services\Notifications\Sms\SmsDriverInterface;
use App\Services\Notifications\WhatsApp\WhatsAppDriverInterface;
use App\Services\Notifications\Telegram\TelegramDriverInterface;
use App\Services\Notifications\GekyChat\GekyChatDriverInterface;
use App\Services\Notifications\Email\EmailDriverInterface;

class Notifier
{
    public function __construct(
        protected SmsDriverInterface $sms,
        protected WhatsAppDriverInterface $whatsApp,
        protected TelegramDriverInterface $telegram,
        protected GekyChatDriverInterface $gekychat,
        protected EmailDriverInterface $email,
    ) {
    }

    /**
     * Send an SMS message.
     *
     * @param string $to Phone number in E.164 format
     * @param string $message Message content
     * @return array
     */
    public function sms(string $to, string $message): array
    {
        return $this->sms->send($to, $message);
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $toE164 Phone number in E.164 format
     * @param string $message Message content
     * @return array
     */
    public function whatsapp(string $toE164, string $message): array
    {
        return $this->whatsApp->sendText($toE164, $message);
    }

    /**
     * Send a Telegram message.
     *
     * @param string $to Chat ID or username
     * @param string $message Message content
     * @return array
     */
    public function telegram(string $to, string $message): array
    {
        return $this->telegram->send($to, $message);
    }

    /**
     * Send a GekyChat message.
     *
     * @param string $to User identifier or phone number
     * @param string $message Message content
     * @return array
     */
    public function gekychat(string $to, string $message): array
    {
        return $this->gekychat->send($to, $message);
    }

    /**
     * Send an email message.
     *
     * @param string $to Email address
     * @param string $message Message content
     * @param string|null $subject Email subject
     * @return array
     */
    public function email(string $to, string $message, ?string $subject = null): array
    {
        return $this->email->send($to, $message, $subject);
    }
}
