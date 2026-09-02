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

    // ── Brand Ads (legado /advertising, Api-Version 1) ────────────────────

    /** BRAND ADS — detalhe de uma campanha (budget, status, datas, strategy). */
    public function getBrandCampaign(int|string $advertiserId, int|string $campaignId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns/{$campaignId}",
            [], [], 0, self::API_VERSION_LEGACY,
        );
    }

    /** BRAND ADS — itens (anuncios) vinculados a campanha. Resposta e lista. */
    public function brandCampaignItems(int|string $advertiserId, int|string $campaignId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns/{$campaignId}/items",
            [], [], 0, self::API_VERSION_LEGACY,
        );
    }

    /** BRAND ADS — keywords da campanha (lista). */
    public function brandCampaignKeywords(int|string $advertiserId, int|string $campaignId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns/{$campaignId}/keywords",
            [], [], 0, self::API_VERSION_LEGACY,
        );
    }

    /** BRAND ADS — metricas por keyword da campanha na janela (retencao 90 dias). */
    public function brandCampaignKeywordsMetrics(int|string $advertiserId, int|string $campaignId, string $dateFrom, string $dateTo): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns/{$campaignId}/keywords/metrics",
            ['date_from' => $dateFrom, 'date_to' => $dateTo],
            [], 0, self::API_VERSION_LEGACY,
        );
    }

    /**
     * BRAND ADS — metricas de TODAS as campanhas do advertiser na janela.
     * `aggregation_type=daily` devolve serie por dia; sem ele vem agregado.
     */
    public function brandCampaignsMetrics(int|string $advertiserId, string $dateFrom, string $dateTo, ?string $aggregationType = 'daily'): array
    {
        $query = ['date_from' => $dateFrom, 'date_to' => $dateTo];
        if ($aggregationType !== null) {
            $query['aggregation_type'] = $aggregationType;
        }

        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns/metrics",
            $query, [], 0, self::API_VERSION_LEGACY,
        );
    }

    /** BRAND ADS — resumo consolidado (`summary[]`) das campanhas na janela. */
    public function brandCampaignsFullSummary(int|string $advertiserId, string $dateFrom, string $dateTo): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/brand_ads/campaigns/full_summary",
            ['date_from' => $dateFrom, 'date_to' => $dateTo],
            [], 0, self::API_VERSION_LEGACY,
        );
    }

    // ── Display (legado /advertising, Api-Version 1) ──────────────────────

    /**
     * DISPLAY — line items de uma campanha. Query opcional: sort_by
     * (start_date...), sort_order (asc|desc).
     *
     * @param  array<string,mixed>  $query
     */
    public function displayCampaignLineItems(int|string $advertiserId, int|string $campaignId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/display/campaigns/{$campaignId}/line_items",
            $query, [], 0, self::API_VERSION_LEGACY,
        );
    }

    /**
     * DISPLAY — criativos de um line item. Query opcional: sort_by, sort_order.
     *
     * @param  array<string,mixed>  $query
     */
    public function displayLineItemCreatives(int|string $advertiserId, int|string $campaignId, int|string $lineItemId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/display/campaigns/{$campaignId}/line_items/{$lineItemId}/creatives",
            $query, [], 0, self::API_VERSION_LEGACY,
        );
    }

    /**
     * DISPLAY — metricas por dimensao. `dimension=line_items` exige
     * `campaign_id`; `dimension=creatives` exige `line_item_id` (passe em
     * $filters). Resposta e lista, 1 objeto por entidade.
     *
     * @param  array<string,mixed>  $filters
     */
    public function displayMetrics(int|string $advertiserId, string $dimension, string $dateFrom, string $dateTo, array $filters = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/advertisers/{$advertiserId}/display/metrics",
            array_merge(['dimension' => $dimension, 'date_from' => $dateFrom, 'date_to' => $dateTo], $filters),
            [], 0, self::API_VERSION_LEGACY,
        );
    }

    // ── Product Ads (namespace /advertising/{site}, Api-Version 2) ────────
    // Sao as rotas da doc "Product Ads para Catalogo e User Products". Aceitam
    // filtros `filters[...]`, `metrics` (CSV), `date_from/date_to` (90 dias),
    // `aggregation_type=DAILY`, `metrics_summary=true`, limit/offset.

    /**
     * PRODUCT ADS — busca campanhas do advertiser: `?filters[status]=active`,
     * `metrics=clicks,cost,...`, `aggregation_type=DAILY`, `metrics_summary`.
     *
     * @param  array<string,mixed>  $query
     */
    public function productAdsCampaignsSearch(string $siteId, int|string $advertiserId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/advertisers/{$advertiserId}/product_ads/campaigns/search",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * PRODUCT ADS — busca ad groups (substitui ads/search). Filtros:
     * `filters[item_ids]=A,B`, `filters[campaigns]`, `filters[statuses]`,
     * `filters[q]`, `filters[domains]`, `filters[official_stores]`,
     * `filters[channel]`, `sort_by`, `sort`, `limit` (ate 800), `metrics`.
     *
     * @param  array<string,mixed>  $query
     */
    public function productAdsAdGroupsSearch(string $siteId, int|string $advertiserId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/advertisers/{$advertiserId}/product_ads/ad_groups/search",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * PRODUCT ADS — busca anuncios (`filters[item_id]`, metrics, datas).
     *
     * @deprecated Marcada como Deprecated na doc; migre pra productAdsAdGroupsSearch().
     * @param  array<string,mixed>  $query
     */
    public function productAdsAdsSearch(string $siteId, int|string $advertiserId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/advertisers/{$advertiserId}/product_ads/ads/search",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * PRODUCT ADS — metricas dos anuncios de uma campanha
     * (`filters[item_ids]`, metrics, date_from/date_to).
     *
     * @deprecated Marcada como Deprecated na doc; use productAdsCampaignAdGroupsMetrics().
     * @param  array<string,mixed>  $query
     */
    public function productAdsCampaignAdsMetrics(string $siteId, int|string $advertiserId, int|string $campaignId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/advertisers/{$advertiserId}/product_ads/campaigns/{$campaignId}/ads/metrics",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * PRODUCT ADS — detalhe de uma campanha (sem advertiser no path). Com
     * `date_from/date_to/metrics` traz as metricas; `aggregation_type=DAILY`
     * devolve LISTA por dia em vez de objeto.
     *
     * @param  array<string,mixed>  $query
     */
    public function getProductAdsCampaign(string $siteId, int|string $campaignId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/product_ads/campaigns/{$campaignId}",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * PRODUCT ADS — metricas dos ad groups de uma campanha. So aceita UM dia
     * (date_to == date_from); pra janela maior passe `filters[ad_group_ids]`.
     *
     * @param  array<string,mixed>  $query
     */
    public function productAdsCampaignAdGroupsMetrics(string $siteId, int|string $campaignId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/product_ads/campaigns/{$campaignId}/ad_groups/metrics",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * PRODUCT ADS — detalhe de um ad group (+ metricas se passar datas/metrics).
     *
     * @param  array<string,mixed>  $query
     */
    public function getProductAdsAdGroup(string $siteId, int|string $adGroupId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/product_ads/ad_groups/{$adGroupId}",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * PRODUCT ADS — anuncios de um ad group com metricas na janela (paginado).
     *
     * @param  array<string,mixed>  $query
     */
    public function productAdsAdGroupAds(string $siteId, int|string $adGroupId, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/advertising/{$siteId}/product_ads/ad_groups/{$adGroupId}/ads",
            $query, [], 0, self::API_VERSION_MARKETPLACE,
        );
    }

    /**
     * Bonificacoes (creditos) de Product Ads do seller autenticado, em nivel
     * de campanha ou de conta. A doc nao mostra header; mandamos Api-Version 1
     * por estar no namespace legado /advertising/advertisers.
     */
    public function bonifications(): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/advertising/advertisers/bonifications',
            [], [], 0, self::API_VERSION_LEGACY,
        );
    }
}
