<?php

namespace App\Services\Notifications\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArkeselSmsDriver implements SmsDriverInterface
{
    public function send(string $to, string $message): array
    {
        $apiKey = env('ARKESEL_SMS_API_KEY');
        $senderId = env('ARKESEL_SMS_SENDER_ID');

        if (!$apiKey) {
            Log::error('[SMS] Missing Arkesel API key');
            return [
                'success' => false,
                'error' => 'Missing Arkesel API key',
            ];
        }

        if (!$senderId) {
            Log::error('[SMS] Missing Arkesel Sender ID');
            return [
                'success' => false,
                'error' => 'Missing Arkesel Sender ID. Please check your Arkesel account for approved sender IDs.',
            ];
        }

        $formattedPhone = ltrim($to, '+');

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://sms.arkesel.com/api/v2/sms/send', [
                'sender' => $senderId,
                'recipients' => [$formattedPhone],
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('[SMS] Arkesel SMS sent successfully', [
                    'to' => $to,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully via Arkesel',
                    'response' => $data,
                    'provider' => 'arkesel',
                ];
            }

            Log::error('[SMS] Arkesel send failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('message', 'Unknown error'),
                'status' => $response->status(),
                'provider' => 'arkesel',
            ];
        } catch (\Exception $e) {
            Log::error('[SMS] Arkesel exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'arkesel',
            ];
        }
    }
}
