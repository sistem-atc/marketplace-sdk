<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Bases;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Exceptions\LojaIntegradaRequestException;
use SistemAtc\Marketplaces\LojaIntegrada\Support\HttpClientFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

abstract class BaseMethods
{
    protected PendingRequest $httpClient;

    public function __construct(
        PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * Executa a chamada com retry (429/5xx, até 3x) e converte falha em
     * LojaIntegradaRequestException.
     *
     * @param array<string,mixed> $query
     * @param array<string,mixed> $body
     * @param array<string,string> $headers   headers extras só desta chamada (ex.: x-correlation-id)
     * @param bool                 $multipart body vai como multipart/form-data (nf, dce, upload)
     * @return array<string,mixed>
     */
    protected function makeRequest(
        HttpMethod $method,
        string $path,
        array $query = [],
        array $body = [],
        int $retryAttempt = 0,
        array $headers = [],
        bool $multipart = false
    ): array {
        $response = $this->executeRequest($method, $path, $query, $body, $headers, $multipart);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1, $headers, $multipart);
        }

        if ($response->failed()) {
            throw new LojaIntegradaRequestException($response);
        }

        return $response->json() ?? [];
    }

    /**
     * @param array<string,mixed>  $query
     * @param array<string,mixed>  $body
     * @param array<string,string> $headers
     */
    protected function executeRequest(
        HttpMethod $method,
        string $path,
        array $query,
        array $body,
        array $headers = [],
        bool $multipart = false
    ): Response {
        // Clona pra não vazar headers/bodyFormat de uma chamada pra outra
        // (PendingRequest é stateful).
        $client = ($headers !== [] || $multipart) ? clone $this->httpClient : $this->httpClient;

        if ($headers !== []) {
            $client = $client->withHeaders($headers);
        }

        if ($multipart) {
            // Laravel converte ['campo' => valor] em partes name/contents; itens já
            // no formato ['name','contents','filename'] passam direto (upload).
            $client = $client->asMultipart();
        }

        // Query string em PUT/PATCH/DELETE (ex.: ?id_externo=1) vai colada na URL.
        if ($query !== [] && $method !== HttpMethod::GET) {
            $path .= (str_contains($path, '?') ? '&' : '?').http_build_query($query);
        }

        return match ($method) {
            HttpMethod::GET => $client->get($path, $query),
            HttpMethod::POST => $client->post($path, $body),
            HttpMethod::PUT => $client->put($path, $body),
            HttpMethod::PATCH => $client->patch($path, $body),
            HttpMethod::DELETE => $client->delete($path, $body),
        };
    }

    /**
     * URL absoluta pra recursos fora do `/v1` (enviali/v2, webhooks/v1, v3/marketing).
     * Deriva da mesma api_base configurada (settings ou config), tirando o sufixo `/v1`.
     */
    protected function rootUrl(string $path): string
    {
        $base = $this->integration->getMarketplaceSettings()['api_base']
            ?? config('marketplaces.lojaintegrada.api_base', 'https://api.awsli.com.br/v1');

        return rtrim((string) preg_replace('#/v1/?$#', '', rtrim((string) $base, '/')), '/').'/'.ltrim($path, '/');
    }
}
