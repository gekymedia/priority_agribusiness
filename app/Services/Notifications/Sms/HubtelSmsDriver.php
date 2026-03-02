<?php

namespace App\Services\Notifications\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HubtelSmsDriver implements SmsDriverInterface
{
    public function send(string $to, string $message): array
    {
        $clientId = env('HUBTEL_CLIENT_ID');
        $clientSecret = env('HUBTEL_CLIENT_SECRET');
        $senderId = env('HUBTEL_SENDER_ID', config('app.name'));

        if (!$clientId || !$clientSecret) {
            Log::error('[SMS] Missing Hubtel credentials');
            return [
                'success' => false,
                'error' => 'Missing Hubtel credentials',
            ];
        }

        $formattedPhone = ltrim($to, '+');

        try {
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->post('https://smsc.hubtel.com/v1/messages/send', [
                    'From' => $senderId,
                    'To' => $formattedPhone,
                    'Content' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('[SMS] Hubtel SMS sent successfully', [
                    'to' => $to,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully via Hubtel',
                    'response' => $data,
                    'provider' => 'hubtel',
                ];
            }

            Log::error('[SMS] Hubtel send failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('message', 'Unknown error'),
                'status' => $response->status(),
                'provider' => 'hubtel',
            ];
        } catch (\Exception $e) {
            Log::error('[SMS] Hubtel exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'hubtel',
            ];
        }
    }
}
