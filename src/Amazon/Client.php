<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Amazon\Endpoints\Notifications;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Amazon\Endpoints\Listings;
use SistemAtc\Marketplaces\Amazon\Endpoints\Messaging;
use SistemAtc\Marketplaces\Amazon\Endpoints\Reports;
use SistemAtc\Marketplaces\Amazon\Endpoints\Invoices;
use SistemAtc\Marketplaces\Amazon\Endpoints\Tokens;
use SistemAtc\Marketplaces\Amazon\Endpoints\Pricing;
use SistemAtc\Marketplaces\Amazon\Support\TokenRefresher;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Cliente HTTP base pra Amazon Selling Partner API.
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

    public function listings(): Listings
    {
        return new Listings($this);
    }

    public function messaging(): Messaging
    {
        return new Messaging($this);
    }

    public function reports(): Reports
    {
        return new Reports($this);
    }

    public function invoices(): Invoices
    {
        return new Invoices($this);
    }

    public function tokens(): Tokens
    {
        return new Tokens($this);
    }

    public function pricing(): Pricing
    {
        return new Pricing($this);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, $query);
    }

    public function post(string $path, array $body = []): array
    {
        return $this->request('POST', $path, body: $body);
    }

    /**
     * GET restrito: obtém um RDT pra (GET, $path) e usa no lugar do token LWA.
     */
    public function getRestricted(string $path, array $query = []): array
    {
        return $this->request('GET', $path, query: $query, tokenOverride: $this->rdtFor('GET', $path));
    }

    /**
     * POST restrito: obtém um RDT pra (POST, $path) e usa no lugar do token LWA.
     */
    public function postRestricted(string $path, array $body = []): array
    {
        return $this->request('POST', $path, body: $body, tokenOverride: $this->rdtFor('POST', $path));
    }

    private function rdtFor(string $method, string $path): ?string
    {
        $rdt = $this->tokens()->createRestrictedDataToken([
            ['method' => $method, 'path' => $path],
        ]);

        return $rdt !== '' ? $rdt : null;
    }

    /**
     * GET grantless: usa um token client_credentials (scope) no lugar do token
     * de seller. Pra operacoes SP-API que nao exigem consent de um seller
     * (ex.: Notifications getDestinations).
     */
    public function getGrantless(string $path, string $scope, array $query = []): array
    {
        return $this->request('GET', $path, query: $query, tokenOverride: TokenRefresher::grantless($this->integration, $scope));
    }

    /** POST grantless (ex.: Notifications createDestination). */
    public function postGrantless(string $path, string $scope, array $body = []): array
    {
        return $this->request('POST', $path, body: $body, tokenOverride: TokenRefresher::grantless($this->integration, $scope));
    }

    /** DELETE grantless (ex.: Notifications deleteDestination / deleteSubscriptionById). */
    public function deleteGrantless(string $path, string $scope): array
    {
        return $this->request('DELETE', $path, tokenOverride: TokenRefresher::grantless($this->integration, $scope));
    }

    public function put(string $path, array $body = []): array
    {
        return $this->request('PUT', $path, body: $body);
    }

    public function patch(string $path, array $body = []): array
    {
        return $this->request('PATCH', $path, body: $body);
    }

    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    private function request(string $method, string $path, array $query = [], array $body = [], ?string $tokenOverride = null): array
    {
        // tokenOverride = RDT (Restricted Data Token) pra operações restritas;
        // senão usa o access token LWA normal.
        $token = $tokenOverride ?? TokenRefresher::refresh($this->integration);

        $settings = $this->integration->getMarketplaceSettings();
        $base = ! empty($settings['endpoint'])
            ? (string) $settings['endpoint']
            : (string) config('marketplaces.amazon.spapi_base_url', 'https://sellingpartnerapi-na.amazon.com');
        $url = rtrim($base, '/').'/'.ltrim($path, '/');

        for ($attempt = 1; $attempt <= self::MAX_RETRIES_ON_THROTTLE; $attempt++) {
            $req = $this->buildRequest($token);
            
            if ($query) {
                $urlWithQuery = $url . '?' . http_build_query($query);
            } else {
                $urlWithQuery = $url;
            }

            $resp = match ($method) {
                'GET' => $req->get($url, $query),
                'POST' => $req->post($urlWithQuery, $body),
                'PUT' => $req->put($urlWithQuery, $body),
                'PATCH' => $req->patch($urlWithQuery, $body),
                'DELETE' => $req->delete($urlWithQuery),
                default => throw new RuntimeException("HTTP method nao suportado: $method"),
            };

            if ($resp->successful()) {
                return $resp->json() ?? [];
            }

            if ($resp->status() === 429 && $attempt < self::MAX_RETRIES_ON_THROTTLE) {
                $sleep = (int) ($resp->header('Retry-After') ?: pow(2, $attempt));
                sleep($sleep);
                continue;
            }

            if ($resp->status() === 401 && $attempt === 1 && $tokenOverride === null) {
                $token = TokenRefresher::refresh($this->integration, force: true);
                continue;
            }
            
            if ($resp->status() === 404) {
                return [];
            }

            $this->handleError($resp);
        }

        return [];
    }

    private function buildRequest(string $token): PendingRequest
    {
        return Http::withHeaders([
            'x-amz-access-token' => $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(30);
    }

    private function handleError(Response $response): void
    {
        Log::error('Amazon API Error', [
            'status' => $response->status(),
            'body' => $response->json(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        
        throw new RuntimeException("Amazon API Error: " . $response->body(), $response->status());
    }
}
