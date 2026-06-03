<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Bases;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Shopee\Support\SignatureGenerator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

abstract class BaseMethods
{
    protected PendingRequest $httpClient;

    public function __construct(
        PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {
        $this->httpClient = $httpClient;
    }

    protected function makeRequest(
        HttpMethod $method,
        string $apiPath,
        array $query = [],
        array $body = [],
        bool $publicApi = false,
        int $retryAttempt = 0
    ): array {
        $apiPath = $this->normalizeApiPath($apiPath);
        $authQuery = $this->buildAuthQuery($apiPath, $publicApi);
        $fullQuery = array_merge($authQuery, $query);

        $response = $this->executeRequest($method, $apiPath, $fullQuery, $body);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->makeRequest($method, $apiPath, $query, $body, $publicApi, $retryAttempt + 1);
        }

        if ($this->isAuthError($response) && $retryAttempt === 0) {
            $this->httpClient = HttpClientFactory::make($this->integration);
            return $this->makeRequest($method, $apiPath, $query, $body, $publicApi, $retryAttempt + 1);
        }

        $data = $response->json() ?? [];
        if ($response->failed() || ! empty($data['error'])) {
            $this->handleError($response);
        }

        return $data;
    }

    protected function executeRequest(
        HttpMethod $method,
        string $apiPath,
        array $query,
        array $body,
    ): Response {
        $client = $this->httpClient;
        return match ($method) {
            HttpMethod::GET => $client->get($apiPath, $query),
            HttpMethod::POST => $client->post($apiPath.'?'.http_build_query($query), $body),
            HttpMethod::PUT => $client->put($apiPath.'?'.http_build_query($query), $body),
            HttpMethod::PATCH => $client->patch($apiPath.'?'.http_build_query($query), $body),
            HttpMethod::DELETE => $client->delete($apiPath.'?'.http_build_query($query), $body),
        };
    }

    protected function buildAuthQuery(string $apiPath, bool $publicApi): array
    {
        $settings = $this->integration->getMarketplaceSettings();
        $partnerId = (int) ($settings['partner_id'] ?? 0);
        $partnerKey = (string) ($settings['partner_key'] ?? '');
        $shopId = (int) ($settings['shop_id'] ?? 0);
        $timestamp = time();

        if ($publicApi) {
            $sign = SignatureGenerator::publicSign($partnerId, $apiPath, $timestamp, $partnerKey);
            return ['partner_id' => $partnerId, 'timestamp' => $timestamp, 'sign' => $sign];
        }

        $accessToken = (string) $this->integration->getAccessToken();
        $sign = SignatureGenerator::shopSign($partnerId, $apiPath, $timestamp, $accessToken, $shopId, $partnerKey);

        return [
            'partner_id' => $partnerId,
            'timestamp' => $timestamp,
            'access_token' => $accessToken,
            'shop_id' => $shopId,
            'sign' => $sign,
        ];
    }

    protected function isAuthError(Response $response): bool
    {
        if ($response->status() === 401 || $response->status() === 403) return true;
        $body = $response->json() ?? [];
        return in_array($body['error'] ?? null, ['error_auth', 'error_invalid_access_token', 'error_access_token_expired'], true);
    }

    protected function normalizeApiPath(string $apiPath): string
    {
        if (! str_starts_with($apiPath, '/')) $apiPath = '/'.$apiPath;
        return preg_replace('#/+#', '/', $apiPath) ?: $apiPath;
    }

    protected function handleError(Response $response): void
    {
        $e = new ShopeeRequestException($response);
        Log::warning('Shopee HTTP Request Error', [
            'status' => $e->status(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        throw $e;
    }
}
