<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Advertising;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Product Ads API — gestão de publicidade no ML.
 */
class AdvertisingMethods extends BaseMethods
{
    /**
     * Consulta métricas de Product Ads de um conjunto de itens.
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
     */
    public function listCampaigns(int|string $sellerId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/advertising/product_ads/campaigns/search", [
            'seller_id' => $sellerId,
        ]);
    }
}
