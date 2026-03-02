<?php

namespace App\Services\Notifications\Email;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LaravelMailDriver implements EmailDriverInterface
{
    public function send(string $to, string $message, ?string $subject = null): array
    {
        try {
            Mail::raw($message, function ($mail) use ($to, $subject) {
                $mail->to($to)
                     ->subject($subject ?? 'Notification from ' . config('app.name'));
            });

            return [
                'success' => true,
                'message' => 'Email sent successfully',
            ];
        } catch (\Exception $e) {
            Log::error('[EMAIL] Send failed', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
