<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Advertising;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Advertising API — publicidade no ML (Product Ads, Display e Brand Ads).
 *
 * Todas as rotas exigem o header `Api-Version`, que MUDA por produto (2 no
 * namespace novo `/marketplace/advertising`, 1 no legado `/advertising`). Sem
 * ele a API devolve 400/404 — é pra isso que o `makeRequest` do BaseMethods
 * ganhou o parâmetro opcional `$headers`.
 *
 * Campo de CUSTO por produto (o que o Bunker consome):
 *   - Product Ads → `metrics.cost` (agregado da janela, por campanha)
 *   - Display     → `consumed_budget` (série DIÁRIA nativa)
 *   - Brand Ads   → `consumed_budget` (série DIÁRIA nativa)
 *
 * RETENÇÃO: Product Ads e Brand Ads = 90 dias duros (a API recusa `date_from`
 * mais antigo). Display não tem limite detectado (2023 ainda responde).
 */
class AdvertisingMethods extends BaseMethods
{
    /** Header exigido pelo namespace novo `/marketplace/advertising` (Product Ads). */
    private const API_VERSION_MARKETPLACE = ['Api-Version' => '2'];

    /** Header exigido pelas rotas legadas `/advertising` (advertisers, Display, Brand). */
    private const API_VERSION_LEGACY = ['Api-Version' => '1'];

    /**
     * Advertisers do seller por produto. `product_id`: PADS (Product Ads) ou
     * DISPLAY. BRAND/BRAND_ADS NÃO existem como product_id (400 "Invalid product
     * id") — Brand Ads reusa o mesmo advertiser_id do PADS.
     *
     * @return array{advertisers?: list<array<string, mixed>>}
     */
    public function listAdvertisers(string $productId = 'PADS'): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/advertising/advertisers',
            ['product_id' => $productId],
            [],
            0,
            self::API_VERSION_LEGACY,
        );
    }

    /**
     * PRODUCT ADS — custo por campanha na JANELA `dateFrom..dateTo`, mais o
     * `metrics_summary` com o total. Uma chamada por dia (`dateFrom === dateTo`)
     * monta a série diária: a aditividade foi PROVADA ao centavo (janela de 3
     * dias == soma dos 3 dias puxados isolados).
     *
     * ⚠️ Só o namespace `/marketplace/advertising/{site}/advertisers/{id}`
     * funciona. O legado `/advertising/product_ads/campaigns/search` é rota
     * morta (400 "Type mismatch." / 500).
     *
     * ⚠️ `metrics_summary=true` EXIGE `metrics` preenchido (senão 400).
     *
     * @param  list<string>  $metrics
     * @return array{results?: list<array<string, mixed>>, metrics_summary?: array<string, mixed>, paging?: array<string, mixed>}
     */
    public function searchProductAdsCampaigns(
        string $siteId,
        int|string $advertiserId,
        string $dateFrom,
        string $dateTo,
        array $metrics = ['cost'],
        int $limit = 50,
        int $offset = 0,
    ): array {
        return $this->makeRequest(
            HttpMethod::GET,
            "/marketplace/advertising/{$siteId}/advertisers/{$advertiserId}/product_ads/campaigns/search",
            [
                'limit' => $limit,
                'offset' => $offset,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'metrics' => implode(',', $metrics),
                'metrics_summary' => 'true',
            ],
            [],
            0,
            self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * DISPLAY — campanhas do advertiser. Sem paginação: vem tudo em `results`.
     *
     * @return array{results?: list<array<string, mixed>>}
     */
    public function listDisplayCampaigns(int|string $advertiserId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/display/campaigns",
            [],
            [],
            0,
            self::API_VERSION_LEGACY,
        );
    }

    /**
     * DISPLAY — métricas DIÁRIAS de uma campanha: `metrics[]` traz 1 objeto por
     * dia da janela numa única chamada, custo = `consumed_budget` (valor DO DIA,
     * não acumulado — verificado).
     *
     * @return array{metrics?: list<array<string, mixed>>, summary?: array<string, mixed>}
     */
    public function displayCampaignMetrics(
        int|string $advertiserId,
        int|string $campaignId,
        string $dateFrom,
        string $dateTo,
    ): array {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/display/campaigns/{$campaignId}/metrics",
            ['date_from' => $dateFrom, 'date_to' => $dateTo],
            [],
            0,
            self::API_VERSION_LEGACY,
        );
    }

    /**
     * BRAND ADS — campanhas do advertiser. Chave `campaigns` (não `results`).
     *
     * @return array{campaigns?: list<array<string, mixed>>}
     */
    public function listBrandCampaigns(int|string $advertiserId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns",
            [],
            [],
            0,
            self::API_VERSION_LEGACY,
        );
    }

    /**
     * BRAND ADS — métricas DIÁRIAS de uma campanha: `metrics[]` traz
     * `{date, metrics: {consumed_budget, ...}}` por dia (valor DO DIA, não
     * acumulado — a curva crescente engana, o probe de dia isolado provou).
     * Retenção de 90 dias.
     *
     * @return array{metrics?: list<array<string, mixed>>, dashboard?: array<string, mixed>, summary?: array<string, mixed>}
     */
    public function brandCampaignMetrics(
        int|string $advertiserId,
        int|string $campaignId,
        string $dateFrom,
        string $dateTo,
    ): array {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns/{$campaignId}/metrics",
            ['date_from' => $dateFrom, 'date_to' => $dateTo],
            [],
            0,
            self::API_VERSION_LEGACY,
        );
    }

    /**
     * Consulta métricas de Product Ads de um conjunto de itens.
     *
     * @deprecated A rota devolve 404 ("No productAd by itemId metrics was found
     *   channel marketplace"). Use searchProductAdsCampaigns() pro custo.
     */
    public function getMetrics(array $itemIds, string $dateFrom, string $dateTo): array
    {
        return $this->makeRequest(HttpMethod::GET, '/advertising/product_ads/ads/metrics', [
            'ids' => implode(',', $itemIds),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }

    /**
     * Altera o status de um anúncio no Product Ads (active/paused).
     */
    public function updateStatus(string $itemId, string $status): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/advertising/product_ads/ads/{$itemId}", [], [
            'status' => $status,
        ]);
    }

    /**
     * Consulta campanhas de publicidade do seller.
     *
     * @deprecated Rota morta (400 "Type mismatch." / 500 pra qualquer param).
     *   Use searchProductAdsCampaigns() no namespace /marketplace/advertising.
     */
    public function listCampaigns(int|string $sellerId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/advertising/product_ads/campaigns/search", [
            'seller_id' => $sellerId,
        ]);
    }
}
