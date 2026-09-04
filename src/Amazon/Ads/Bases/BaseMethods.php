<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Bases;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;

/**
 * Base da AMAZON ADS API (advertising-api.amazon.com — região NA cobre o BR).
 *
 * Difere da SP-API em tudo: host próprio, auth por LwA puro (header
 * `Amazon-Advertising-API-ClientId` + Bearer), e o escopo de CONTA vai no
 * header `Amazon-Advertising-API-Scope` (profileId do anunciante no
 * marketplace) — obrigatório em quase todas as rotas menos /v2/profiles.
 *
 * O access token (1h) é responsabilidade do CONSUMIDOR renovar (via
 * Support\OAuth::refreshAccessToken) e chega pronto pelo accessToken().
 */
abstract class BaseMethods
{
    public function __construct(
        protected MarketplaceIntegration $integration,
        protected string $clientId,
    ) {}

    protected function baseUrl(): string
    {
        return (string) config('marketplaces.amazon_ads.base_url', 'https://advertising-api.amazon.com');
    }

    /**
     * @param  string|null  $accept  media type do Accept. Null = `application/json`.
     *   Passar aqui (e nao via withHeaders depois) evita o Accept duplo:
     *   acceptJson() + withHeaders(Accept) ACUMULAM no Guzzle e a Ads API
     *   responde 406 em varias rotas v3/v4.
     */
    protected function http(?string $profileId = null, ?string $accept = null): PendingRequest
    {
        $headers = [
            'Amazon-Advertising-API-ClientId' => $this->clientId,
            'Authorization' => 'Bearer '.$this->integration->getAccessToken(),
            'Accept' => $accept ?? 'application/json',
        ];

        if ($profileId !== null) {
            $headers['Amazon-Advertising-API-Scope'] = $profileId;
        }

        return Http::withHeaders($headers);
    }

    /** @throws AmazonAdsRequestException */
    protected function decodeOrFail(\Illuminate\Http\Client\Response $response, string $contexto): array
    {
        $data = $response->json();

        if (! $response->successful()) {
            $msg = is_array($data)
                ? (string) ($data['message'] ?? $data['details'] ?? $response->body())
                : $response->body();

            throw new AmazonAdsRequestException("Amazon Ads {$contexto}: {$msg}", $response->status());
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Chamada generica a Ads API.
     *
     * A maioria das rotas v3 exige media types proprios (ex.:
     * `application/vnd.spCampaign.v3+json`) tanto em Content-Type quanto em
     * Accept — sem eles a API devolve 415/406. Passe em `$contentType` (vale pros
     * dois; use `$accept` se forem diferentes). Retry em 429 respeitando
     * Retry-After (ate 3 tentativas). 4xx/5xx viram AmazonAdsRequestException.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|list<mixed>  $body
     * @param  array<string, string>  $headers  extras desta chamada
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    protected function request(
        string $method,
        string $path,
        ?string $profileId = null,
        array $query = [],
        array $body = [],
        ?string $contentType = null,
        ?string $accept = null,
        array $headers = [],
    ): array {
        $url = $this->baseUrl().'/'.ltrim($path, '/');
        $method = strtoupper($method);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $client = $this->http($profileId, $accept ?? $contentType)->withHeaders($headers);

            if ($contentType !== null) {
                $client = $client->withHeaders(['Content-Type' => $contentType]);
            }

            // Body vazio vira `{}` (objeto), nao `[]`: as listagens v3/v4 sem filtro
            // esperam um objeto JSON e rejeitam array.
            $json = $body === [] ? '{}' : (string) json_encode($body);

            $urlWithQuery = $query !== [] ? $url.'?'.http_build_query($query) : $url;

            $response = match ($method) {
                'GET' => $client->get($url, $query),
                'DELETE' => $body !== [] ? $client->withBody($json, $contentType ?? 'application/json')->delete($urlWithQuery) : $client->delete($urlWithQuery),
                'POST', 'PUT', 'PATCH' => $client->withBody($json, $contentType ?? 'application/json')->send($method, $urlWithQuery),
                default => throw new \InvalidArgumentException("HTTP method nao suportado: {$method}"),
            };

            if ($response->status() === 429 && $attempt < 3) {
                sleep((int) ($response->header('Retry-After') ?: 2 ** $attempt));
                continue;
            }

            return $this->decodeOrFail($response, "{$method} {$path}");
        }

        return [];
    }
}
