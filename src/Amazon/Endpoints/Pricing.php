<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Product Pricing API. Diferente do "your price" do relatorio
 * (GET_MERCHANT_LISTINGS_ALL_DATA), aqui temos o preco EXIBIDO ao cliente
 * (Buy Box / featured offer), ja' com deal/oferta aplicado.
 *
 * IMPORTANTE: pra anuncio com variacao, use o ASIN-FILHO — o VARIATION_PARENT
 * nao tem oferta (retorna vazio/NoBuyableOffers).
 */
class Pricing
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Ofertas de UM ASIN (v0). Resposta em `payload.Summary.BuyBoxPrices` (preco
     * exibido) + `LowestPrices`. Rate-limit ~0.5 req/s.
     *
     * GET /products/pricing/v0/items/{Asin}/offers
     *
     * @return array<string, mixed>
     */
    public function getItemOffers(string $asin, string $marketplaceId, string $condition = 'New'): array
    {
        return $this->client->get("/products/pricing/v0/items/{$asin}/offers", [
            'MarketplaceId' => $marketplaceId,
            'ItemCondition' => $condition,
        ]);
    }

    /**
     * Resumo competitivo (v2022-05-01, BATCH) de varios ASINs numa chamada —
     * `lowestPricedOffers` (oferta mais barata = exibida p/ seller unico) e
     * `referencePrices`. Mais eficiente que o getItemOffers 1-a-1.
     *
     * POST /batches/products/pricing/2022-05-01/items/competitiveSummary
     *
     * @param  array<int, string>  $asins
     * @param  array<int, string>  $includedData
     * @return array<string, mixed>
     */
    public function competitiveSummary(array $asins, string $marketplaceId, array $includedData = ['lowestPricedOffers', 'referencePrices']): array
    {
        $requests = [];
        foreach ($asins as $asin) {
            $requests[] = [
                'asin' => $asin,
                'marketplaceId' => $marketplaceId,
                'includedData' => $includedData,
                'method' => 'GET',
                'uri' => '/products/pricing/2022-05-01/items/competitiveSummary',
            ];
        }

        return $this->client->post('/batches/products/pricing/2022-05-01/items/competitiveSummary', [
            'requests' => $requests,
        ]);
    }
}
