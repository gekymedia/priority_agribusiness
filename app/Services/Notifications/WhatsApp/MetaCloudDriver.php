<?php

namespace App\Services\Notifications\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCloudDriver implements WhatsAppDriverInterface
{
    public function sendText(string $toE164, string $message): array
    {
        $phoneId = env('WA_CLOUD_PHONE_ID');
        $token = env('WA_CLOUD_TOKEN');

        if (!$phoneId || !$token) {
            Log::error('[WHATSAPP] Missing Meta Cloud API credentials');
            return [
                'success' => false,
                'error' => 'Missing WhatsApp Cloud API credentials',
            ];
        }

        $formattedPhone = ltrim($toE164, '+');

        try {
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $formattedPhone,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('[WHATSAPP] Message sent successfully', [
                    'to' => $toE164,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => 'WhatsApp message sent successfully',
                    'response' => $data,
                ];
            }

            Log::error('[WHATSAPP] Send failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('error.message', 'Unknown error'),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('[WHATSAPP] Exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
