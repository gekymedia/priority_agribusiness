<?php

namespace App\Services\Notifications\GekyChat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GekyChatApiDriver implements GekyChatDriverInterface
{
    protected string $baseUrl;
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $accessToken = null;
    protected int $systemBotUserId;

    public function __construct()
    {
        $defaultUrl = env('APP_ENV') === 'local' 
            ? 'http://api.gekychat.test' 
            : 'https://api.gekychat.com';
        $this->baseUrl = rtrim(config('notifications.gekychat.api_url', $defaultUrl), '/');
        $this->clientId = config('notifications.gekychat.client_id');
        $this->clientSecret = config('notifications.gekychat.client_secret');
        $this->systemBotUserId = (int) config('notifications.gekychat.system_bot_user_id', 0);
        
        if (empty($this->baseUrl) || $this->baseUrl === 'http://api.gekychat.test' || $this->baseUrl === 'https://api.gekychat.com') {
            $this->baseUrl = rtrim(env('GEKYCHAT_API_URL', $defaultUrl), '/');
        }
        if (empty($this->clientId)) {
            $this->clientId = env('GEKYCHAT_CLIENT_ID');
        }
        if (empty($this->clientSecret)) {
            $this->clientSecret = env('GEKYCHAT_CLIENT_SECRET');
        }
        if ($this->systemBotUserId === 0) {
            $this->systemBotUserId = (int) env('GEKYCHAT_SYSTEM_BOT_USER_ID', 0);
        }
        
        if (empty($this->clientId) || empty($this->clientSecret)) {
            Log::warning('GekyChat credentials not configured', [
                'has_client_id' => !empty($this->clientId),
                'has_client_secret' => !empty($this->clientSecret),
            ]);
        }
    }

    public function send(string $to, string $message): array
    {
        return $this->sendMessageByPhone($to, $message);
    }

    public function sendMessageByPhone(string $phoneNumber, string $message, array $metadata = []): array
    {
        try {
            Log::info('GekyChat sendMessageByPhone: Starting', [
                'phone' => $phoneNumber,
                'message_length' => strlen($message),
                'base_url' => $this->baseUrl,
            ]);
            
            $token = $this->getAccessToken();
            if (!$token) {
                $error = 'Failed to obtain access token. Check logs for OAuth details.';
                Log::error('GekyChat sendMessageByPhone: Token failed', [
                    'phone' => $phoneNumber,
                    'base_url' => $this->baseUrl,
                ]);
                return [
                    'success' => false,
                    'error' => $error
                ];
            }

            $normalizedPhone = $this->normalizePhone($phoneNumber);

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("{$this->baseUrl}/platform/messages/send-to-phone", [
                    'phone' => $normalizedPhone,
                    'body' => $message,
                    'metadata' => $metadata ?? [],
                    'bot_user_id' => $this->systemBotUserId > 0 ? $this->systemBotUserId : null,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'GekyChat message sent successfully',
                    'message_id' => $data['data']['message_id'] ?? null,
                    'conversation_id' => $data['data']['conversation_id'] ?? null,
                    'user_id' => $data['data']['user_id'] ?? null,
                ];
            }

            if ($response->status() === 404) {
                return $this->sendMessageMultiStep($normalizedPhone, $message, $metadata);
            }

            $errorData = $response->json();
            Log::error('[GEKYCHAT] Send failed', ['response' => $errorData]);
            return [
                'success' => false,
                'error' => $errorData['error'] ?? 'API request failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];

        } catch (\Throwable $e) {
            Log::error('GekyChat sendMessageByPhone failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function sendMessageMultiStep(string $normalizedPhone, string $message, array $metadata = []): array
    {
        $userId = $this->findOrCreateUser($normalizedPhone);
        if (!$userId) {
            return [
                'success' => false,
                'error' => 'Failed to find or create user'
            ];
        }

        $conversationId = $this->findOrCreateConversation($userId);
        if (!$conversationId) {
            return [
                'success' => false,
                'error' => 'Failed to find or create conversation'
            ];
        }

        return $this->sendMessageToConversation($conversationId, $message, $metadata);
    }

    protected function getAccessToken(): ?string
    {
        $cacheKey = 'gekychat_access_token_' . md5($this->clientId ?? 'default');
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        if (empty($this->clientId) || empty($this->clientSecret)) {
            Log::error('GekyChat OAuth: Missing credentials');
            return null;
        }

        try {
            $endpoint = "{$this->baseUrl}/platform/oauth/token";
            
            $response = Http::asForm()
                ->timeout(10)
                ->post($endpoint, [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                
                if ($token) {
                    Cache::put($cacheKey, $token, now()->addHour());
                    return $token;
                }
            }

            Log::warning('GekyChat OAuth failed', [
                'status' => $response->status(),
                'response' => substr($response->body(), 0, 500),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('GekyChat OAuth error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $plus = str_starts_with($phone, '+') ? '+' : '';
        $digits = preg_replace('/\D+/', '', $phone);
        
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return '+233' . substr($digits, 1);
        }
        
        if (strlen($digits) === 10 && !$plus) {
            return '+233' . substr($digits, 1);
        }
        
        return $plus . $digits;
    }

    protected function findOrCreateUser(string $normalizedPhone): ?int
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = Http::withToken($token)
                ->timeout(10)
                ->get("{$this->baseUrl}/platform/users/by-phone", [
                    'phone' => $normalizedPhone
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['id'])) {
                    return (int) $data['data']['id'];
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('GekyChat findOrCreateUser error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function findOrCreateConversation(int $userId): ?int
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = Http::withToken($token)
                ->timeout(10)
                ->get("{$this->baseUrl}/platform/conversations/find-or-create", [
                    'user_id' => $userId,
                    'bot_user_id' => $this->systemBotUserId,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['conversation_id'])) {
                    return (int) $data['data']['conversation_id'];
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('GekyChat findOrCreateConversation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function sendMessageToConversation(int $conversationId, string $message, array $metadata = []): array
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return ['success' => false, 'error' => 'No access token'];
            }

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("{$this->baseUrl}/platform/messages/send", [
                    'conversation_id' => $conversationId,
                    'body' => $message,
                    'metadata' => $metadata ?? [],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'GekyChat message sent successfully',
                    'message_id' => $data['data']['message_id'] ?? null,
                    'conversation_id' => $data['data']['conversation_id'] ?? $conversationId,
                ];
            }

            return [
                'success' => false,
                'error' => 'API request failed',
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('GekyChat sendMessageToConversation error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
