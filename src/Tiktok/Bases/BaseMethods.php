<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Bases;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokRequestException;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tiktok\Support\SignatureGenerator;
use SistemAtc\Marketplaces\Tiktok\Support\TokenRefresher;
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
        ?array $body = null,
        int $retryAttempt = 0
    ): array {
        $apiPath = $this->normalizeApiPath($apiPath);
        $fullQuery = $this->buildSignedQuery($apiPath, $query, $body);
        $response = $this->executeRequest($method, $apiPath, $fullQuery, $body);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->makeRequest($method, $apiPath, $query, $body, $retryAttempt + 1);
        }

        if ($this->isAuthError($response) && $retryAttempt === 0) {
            TokenRefresher::forceRefresh($this->integration);
            $this->httpClient = HttpClientFactory::make($this->integration);
            return $this->makeRequest($method, $apiPath, $query, $body, $retryAttempt + 1);
        }

        $data = $response->json() ?? [];
        if ($response->failed() || ($data['code'] ?? 0) !== 0) {
            $this->handleError($response);
        }

        return $data;
    }

    protected function executeRequest(
        HttpMethod $method,
        string $apiPath,
        array $query,
        ?array $body,
    ): Response {
        $client = $this->httpClient;
        $url = $apiPath.'?'.http_build_query($query);

        return match ($method) {
            HttpMethod::GET => $client->get($url),
            HttpMethod::DELETE => $client->delete($url, $body ?? []),
            HttpMethod::POST => $client->post($url, $body ?? []),
            HttpMethod::PUT => $client->put($url, $body ?? []),
            HttpMethod::PATCH => $client->patch($url, $body ?? []),
        };
    }

    protected function buildSignedQuery(string $apiPath, array $businessQuery, ?array $body): array
    {
        $settings = $this->integration->getMarketplaceSettings();
        $shopCipher = (string) ($settings['shop_cipher'] ?? '');
        $appKey = (string) ($settings['app_key'] ?? '');
        $appSecret = (string) ($settings['app_secret'] ?? '');

        if ($appKey === '' || $appSecret === '') {
            throw new InvalidArgumentException('TikTok integration missing app_key or app_secret.');
        }

        $authQuery = ['app_key' => $appKey, 'timestamp' => time()];
        if ($shopCipher !== '') $authQuery['shop_cipher'] = $shopCipher;

        $merged = array_merge($authQuery, $businessQuery);
        $bodyJson = $body !== null ? json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;

        $merged['sign'] = SignatureGenerator::sign($apiPath, $merged, $bodyJson, $appSecret);
        return $merged;
    }

    protected function isAuthError(Response $response): bool
    {
        if ($response->status() === 401 || $response->status() === 403) return true;
        $body = $response->json() ?? [];
        return in_array($body['code'] ?? null, [105002, 105004, 105005], true);
    }

    protected function normalizeApiPath(string $apiPath): string
    {
        if (! str_starts_with($apiPath, '/')) $apiPath = '/'.$apiPath;
        return preg_replace('#/+#', '/', $apiPath) ?: $apiPath;
    }

    protected function handleError(Response $response): void
    {
        $e = new TiktokRequestException($response);
        Log::warning('TikTok HTTP Request Error', [
            'status' => $e->status(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        throw $e;
    }
}
