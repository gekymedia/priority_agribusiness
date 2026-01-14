<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Generic API Client for Priority Bank Central Finance API
 */
class PriorityBankApiClient
{
    protected string $baseUrl;
    protected string $apiToken;
    protected int $timeout;
    protected int $maxRetries;

    public function __construct(?string $baseUrl = null, ?string $apiToken = null)
    {
        $this->baseUrl = $baseUrl ?? config('services.priority_bank.api_url', 'http://localhost:8000');
        $this->apiToken = $apiToken ?? config('services.priority_bank.api_token');
        $this->timeout = config('services.priority_bank.timeout', 10);
        $this->maxRetries = config('services.priority_bank.max_retries', 3);
    }

    public function pushIncome(
        string $systemId,
        string $externalTransactionId,
        float $amount,
        string $date,
        string $channel,
        array $options = []
    ): ?array {
        $idempotencyKey = $options['idempotency_key'] ?? $this->generateIdempotencyKey($systemId, $externalTransactionId);

        $payload = array_merge([
            'system_id' => $systemId,
            'external_transaction_id' => $externalTransactionId,
            'amount' => $amount,
            'date' => $date,
            'channel' => $channel,
        ], $options);

        return $this->makeRequest('POST', '/api/central-finance/income', $payload, $idempotencyKey);
    }

    public function pushExpense(
        string $systemId,
        string $externalTransactionId,
        float $amount,
        string $date,
        string $channel,
        array $options = []
    ): ?array {
        $idempotencyKey = $options['idempotency_key'] ?? $this->generateIdempotencyKey($systemId, $externalTransactionId);

        $payload = array_merge([
            'system_id' => $systemId,
            'external_transaction_id' => $externalTransactionId,
            'amount' => $amount,
            'date' => $date,
            'channel' => $channel,
        ], $options);

        return $this->makeRequest('POST', '/api/central-finance/expense', $payload, $idempotencyKey);
    }

    protected function makeRequest(string $method, string $endpoint, array $payload, ?string $idempotencyKey = null): ?array
    {
        $url = rtrim($this->baseUrl, '/') . $endpoint;
        $attempt = 0;

        while ($attempt < $this->maxRetries) {
            try {
                $headers = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ];

                if ($this->apiToken) {
                    $headers['Authorization'] = 'Bearer ' . $this->apiToken;
                }

                if ($idempotencyKey) {
                    $headers['X-Idempotency-Key'] = $idempotencyKey;
                }

                $response = Http::timeout($this->timeout)
                    ->withHeaders($headers)
                    ->{strtolower($method)}($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Priority Bank API request successful', [
                        'endpoint' => $endpoint,
                        'system_id' => $payload['system_id'] ?? null,
                        'transaction_id' => $payload['external_transaction_id'] ?? null,
                    ]);
                    return $data;
                }

                if ($response->status() >= 400 && $response->status() < 500) {
                    Log::warning('Priority Bank API client error (no retry)', [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);
                    return null;
                }

                $attempt++;
                if ($attempt < $this->maxRetries) {
                    $delay = pow(2, $attempt);
                    Log::warning("Priority Bank API server error, retrying in {$delay}s", [
                        'endpoint' => $endpoint,
                        'attempt' => $attempt,
                        'status' => $response->status(),
                    ]);
                    sleep($delay);
                }

            } catch (\Exception $e) {
                $attempt++;
                if ($attempt >= $this->maxRetries) {
                    Log::error('Priority Bank API request failed after retries', [
                        'endpoint' => $endpoint,
                        'error' => $e->getMessage(),
                        'attempts' => $attempt,
                    ]);
                    return null;
                }

                $delay = pow(2, $attempt);
                Log::warning("Priority Bank API exception, retrying in {$delay}s", [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                ]);
                sleep($delay);
            }
        }

        return null;
    }

    protected function generateIdempotencyKey(string $systemId, string $externalTransactionId): string
    {
        return hash('sha256', "{$systemId}:{$externalTransactionId}");
    }
}

