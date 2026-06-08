<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Amazon\Endpoints\Notifications;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Amazon\Support\TokenRefresher;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Cliente HTTP base pra Amazon Selling Partner API.
 *
 * - Token Bearer LWA via TokenRefresher (refresh on-demand idempotente)
 * - Header `x-amz-access-token` (Amazon usa esse, nao Authorization padrao)
 * - Rate-limit handler: respeita Retry-After no 429 (sleep + retry ate 3x)
 * - 401 mid-flight → force refresh + retry 1x
 * - 404 → [] (caller decide o que fazer com array vazio)
 * - Sem AWS Sigv4 (descontinuado pela Amazon em out/2023)
 *
 * Endpoints tipados vivem em Endpoints/* e recebem este client.
 */
class Client
{
    private const MAX_RETRIES_ON_THROTTLE = 3;

    public function __construct(
        private readonly MarketplaceIntegration $integration,
    ) {}

    public function orders(): Orders
    {
        return new Orders($this);
    }

    public function finances(): Finances
    {
        return new Finances($this);
    }

    public function notifications(): Notifications
    {
        return new Notifications($this);
    }

    /**
     * GET com auto-refresh de token + retry em 429.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, $query);
    }

    /**
     * POST com body JSON.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function post(string $path, array $body = []): array
    {
        return $this->request('POST', $path, body: $body);
    }

    /**
     * DELETE (sem body) — destinations/subscriptions Notifications API.
     *
     * @return array<string, mixed>
     */
    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $query = [], array $body = []): array
    {
        $token = TokenRefresher::refresh($this->integration);

        // Endpoint regional pode ser per-Integration (NA/EU/FE). URL e'
        // publica do vendor — default em config quando settings vazio.
        $settings = $this->integration->getMarketplaceSettings();
        $base = ! empty($settings['endpoint'])
            ? (string) $settings['endpoint']
            : (string) config('marketplaces.amazon.spapi_base_url', 'https://sellingpartnerapi-na.amazon.com');
        $url = rtrim($base, '/').'/'.ltrim($path, '/');

        for ($attempt = 1; $attempt <= self::MAX_RETRIES_ON_THROTTLE; $attempt++) {
            $req = $this->buildRequest($token);
            $resp = match ($method) {
                'GET' => $req->get($url, $query),
                'POST' => $req->post($url, $body),
                'DELETE' => $req->delete($url),
                default => throw new RuntimeException("HTTP method nao suportado: $method"),
            };

            if ($resp->successful()) {
                return $resp->json() ?? [];
            }

            // Throttle — backoff exponencial + Retry-After header
            if ($resp->status() === 429 && $attempt < self::MAX_RETRIES_ON_THROTTLE) {
                $sleep = (int) ($resp->header('Retry-After') ?: pow(2, $attempt));
                Log::warning('Amazon Client: 429 throttle, retrying', [
                    'integration_id' => $this->integration->getIntegrationIdentifier(),
                    'path' => $path,
                    'attempt' => $attempt,
                    'sleep' => $sleep,
                ]);
                sleep($sleep);

                continue;
            }

            // Token expirado mid-flight — force refresh + retry 1x
            if ($resp->status() === 401 && $attempt === 1) {
                $token = TokenRefresher::refresh($this->integration, force: true);

                continue;
            }

            return $this->failOrThrow($resp, $path);
        }

        return [];
    }

    private function buildRequest(string $token): PendingRequest
    {
        return Http::withHeaders([
            'x-amz-access-token' => $token,
            'Accept' => 'application/json',
            'User-Agent' => 'SistemAtcMarketplaces/1.0 (Language=PHP)',
        ])->timeout(30);
    }

    /**
     * 404 → [] (recurso inexistente); demais erros → throw.
     *
     * @return array<string, mixed>
     */
    private function failOrThrow(Response $resp, string $path): array
    {
        if ($resp->status() === 404) {
            return [];
        }

        $errorCode = $resp->json('errors.0.code') ?? 'unknown';
        $errorMsg = $resp->json('errors.0.message') ?? 'unknown';

        Log::error('Amazon Client: request falhou', [
            'integration_id' => $this->integration->getIntegrationIdentifier(),
            'path' => $path,
            'status' => $resp->status(),
            'error_code' => $errorCode,
            'error_msg' => $errorMsg,
        ]);

        throw new RuntimeException(
            "SP-API {$path} retornou HTTP {$resp->status()}: $errorCode — $errorMsg"
        );
    }
}
