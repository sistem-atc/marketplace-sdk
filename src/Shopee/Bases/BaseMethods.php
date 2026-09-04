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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

abstract class BaseMethods
{
    /**
     * Entidade que assina as chamadas NAO-publicas deste grupo de endpoints.
     *
     * A Shopee tem 4 "api_type" autenticados e todos assinam do mesmo jeito
     * (partner_id + path + timestamp + access_token + <id da entidade>), mudando
     * so' QUAL id entra na base string e na query:
     *   - Shop      -> shop_id       (default; 333 APIs)
     *   - Merchant  -> merchant_id   (global_product, merchant; use $merchantApi)
     *   - Principal -> principal_id  (principal/*, Brand Portal)
     *   - User      -> user_id       (livestream/*)
     * Subclasse de grupo Principal/User sobrescreve esta propriedade; o valor
     * vem de settings[<entidade>] da integration. O access_token TAMBEM e' por
     * entidade (token de shop nao serve pra merchant/principal/user) — a
     * integration usada precisa ter sido autorizada pra aquela entidade.
     */
    protected string $authEntity = 'shop_id';

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
        int $retryAttempt = 0,
        bool $merchantApi = false,
    ): array {
        $apiPath = $this->normalizeApiPath($apiPath);
        $authQuery = $this->buildAuthQuery($apiPath, $publicApi, $merchantApi);
        $fullQuery = array_merge($authQuery, $query);

        $response = $this->executeRequest($method, $apiPath, $fullQuery, $body);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->makeRequest($method, $apiPath, $query, $body, $publicApi, $retryAttempt + 1, $merchantApi);
        }

        if ($this->isAuthError($response) && $retryAttempt === 0) {
            $this->httpClient = HttpClientFactory::make($this->integration);
            return $this->makeRequest($method, $apiPath, $query, $body, $publicApi, $retryAttempt + 1, $merchantApi);
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

    /**
     * Monta partner_id/timestamp/sign (+ access_token e id da entidade nas APIs
     * autenticadas). $merchantApi=true troca shop_id por merchant_id (APIs
     * api_type "Merchant"); caso contrario usa $this->authEntity (shop_id por
     * default; principal_id/user_id em grupos que sobrescrevem).
     */
    protected function buildAuthQuery(string $apiPath, bool $publicApi, bool $merchantApi = false): array
    {
        $settings = $this->integration->getMarketplaceSettings();
        $partnerId = (int) ($settings['partner_id'] ?? 0);
        $partnerKey = (string) ($settings['partner_key'] ?? '');
        $timestamp = time();

        if ($publicApi) {
            $sign = SignatureGenerator::publicSign($partnerId, $apiPath, $timestamp, $partnerKey);
            return ['partner_id' => $partnerId, 'timestamp' => $timestamp, 'sign' => $sign];
        }

        $entityKey = $merchantApi ? 'merchant_id' : $this->authEntity;
        $entityId = (int) ($settings[$entityKey] ?? 0);
        $accessToken = (string) $this->integration->getAccessToken();
        $sign = SignatureGenerator::entitySign($partnerId, $apiPath, $timestamp, $accessToken, $entityId, $partnerKey);

        return [
            'partner_id' => $partnerId,
            'timestamp' => $timestamp,
            'access_token' => $accessToken,
            $entityKey => $entityId,
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

    /**
     * Client multipart pra upload de arquivo (imagem/video/documento).
     *
     * O client da factory vem com asJson(), que fixa `Content-Type:
     * application/json`; attach() troca o body pra multipart mas NAO o header,
     * e o Guzzle so' poe o boundary quando o Content-Type ainda nao existe —
     * a Shopee receberia multipart rotulado como JSON. Por isso o client e'
     * reconstruido aqui, sem asJson(). O token ja' foi renovado pela factory
     * quando esta classe foi instanciada.
     */
    protected function multipartClient(): PendingRequest
    {
        return Http::baseUrl((string) config('marketplaces.shopee.base_url', 'https://partner.shopeemobile.com'))
            ->timeout(60)
            ->connectTimeout(10)
            ->acceptJson()
            ->asMultipart();
    }
}
