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

    protected function http(?string $profileId = null): PendingRequest
    {
        $headers = [
            'Amazon-Advertising-API-ClientId' => $this->clientId,
            'Authorization' => 'Bearer '.$this->integration->getAccessToken(),
        ];

        if ($profileId !== null) {
            $headers['Amazon-Advertising-API-Scope'] = $profileId;
        }

        return Http::withHeaders($headers)->acceptJson();
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
}
