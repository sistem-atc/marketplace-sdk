<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing\ItemOffersResponseDTO;

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
     */
    public function getItemOffers(string $asin, string $marketplaceId, string $condition = 'New'): ItemOffersResponseDTO
    {
        $resp = $this->client->get("/products/pricing/v0/items/{$asin}/offers", [
            'MarketplaceId' => $marketplaceId,
            'ItemCondition' => $condition,
        ]);

        return ItemOffersResponseDTO::fromArray((array) data_get($resp, 'payload', []));
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

    /**
     * Preco (v0) de ate 20 ASINs OU SKUs (GET /products/pricing/v0/price).
     * `ItemType` = `Asin` ou `Sku`; ids vao em `Asins`/`Skus` (CSV). Opcional:
     * `ItemCondition`, `OfferType` (B2C/B2B). Rate limit 0.5 req/s, burst 1.
     * Resposta em `payload[]` (status/ASIN/SellerSKU/Product.Offers).
     *
     * @param  array<int,string>  $ids  ASINs ou SKUs, conforme $itemType
     * @param  array<string,mixed>  $query  ItemCondition, OfferType
     */
    public function getPricing(string $marketplaceId, string $itemType, array $ids, array $query = []): array
    {
        return $this->client->get('/products/pricing/v0/price', $this->csvQuery([
            'MarketplaceId' => $marketplaceId,
            'ItemType' => $itemType,
            ($itemType === 'Sku' ? 'Skus' : 'Asins') => $ids,
        ] + $query));
    }

    /**
     * Preco competitivo (v0) de ate 20 ASINs OU SKUs
     * (GET /products/pricing/v0/competitivePrice). `ItemType` = `Asin`|`Sku`;
     * opcional `CustomerType` (Consumer/Business). Rate limit 0.5 req/s,
     * burst 1. Resposta em `payload[]` (Product.CompetitivePricing).
     *
     * @param  array<int,string>  $ids
     * @param  array<string,mixed>  $query  CustomerType
     */
    public function getCompetitivePricing(string $marketplaceId, string $itemType, array $ids, array $query = []): array
    {
        return $this->client->get('/products/pricing/v0/competitivePrice', $this->csvQuery([
            'MarketplaceId' => $marketplaceId,
            'ItemType' => $itemType,
            ($itemType === 'Sku' ? 'Skus' : 'Asins') => $ids,
        ] + $query));
    }

    /**
     * Ofertas (v0) de um SKU proprio
     * (GET /products/pricing/v0/listings/{SellerSKU}/offers). Rate limit
     * 1 req/s, burst 2. Resposta em `payload.Summary` + `payload.Offers[]`.
     *
     * @param  array<string,mixed>  $query  CustomerType
     */
    public function getListingOffers(string $sellerSku, string $marketplaceId, string $itemCondition = 'New', array $query = []): array
    {
        return $this->client->get('/products/pricing/v0/listings/'.rawurlencode($sellerSku).'/offers', [
            'MarketplaceId' => $marketplaceId,
            'ItemCondition' => $itemCondition,
        ] + $query);
    }

    /**
     * Batch (ate 20) de getItemOffers (POST /batches/products/pricing/v0/itemOffers).
     * Rate limit 0.1 req/s, burst 1. Cada request: `{uri, method:'GET',
     * MarketplaceId, ItemCondition, CustomerType?}` com uri
     * `/products/pricing/v0/items/{Asin}/offers`. Resposta em `responses[]`
     * (cada uma com `status`, `headers`, `body.payload`).
     *
     * @param  array<int,array<string,mixed>>  $requests
     */
    public function getItemOffersBatch(array $requests): array
    {
        return $this->client->post('/batches/products/pricing/v0/itemOffers', ['requests' => $requests]);
    }

    /**
     * Batch (ate 20) de getListingOffers
     * (POST /batches/products/pricing/v0/listingOffers). Rate limit 0.5 req/s,
     * burst 1. Cada request: `{uri, method:'GET', MarketplaceId, ItemCondition,
     * CustomerType?}` com uri `/products/pricing/v0/listings/{SellerSKU}/offers`.
     * Resposta em `responses[]`.
     *
     * @param  array<int,array<string,mixed>>  $requests
     */
    public function getListingOffersBatch(array $requests): array
    {
        return $this->client->post('/batches/products/pricing/v0/listingOffers', ['requests' => $requests]);
    }

    /**
     * Preco esperado pra virar Featured Offer (Buy Box), em batch (ate 40)
     * (POST /batches/products/pricing/2022-05-01/offer/featuredOfferExpectedPrice).
     * Rate limit 0.033 req/s, burst 1. Cada request: `{uri, method:'GET',
     * marketplaceId, sku}` com uri
     * `/products/pricing/2022-05-01/offer/featuredOfferExpectedPrice`.
     * Resposta em `responses[]` (body.featuredOfferExpectedPriceResults[]).
     *
     * @param  array<int,array<string,mixed>>  $requests
     */
    public function getFeaturedOfferExpectedPriceBatch(array $requests): array
    {
        return $this->client->post(
            '/batches/products/pricing/2022-05-01/offer/featuredOfferExpectedPrice',
            ['requests' => $requests],
        );
    }

    /**
     * Serializa arrays da query em CSV (Asins/Skus).
     *
     * @param  array<string,mixed>  $query
     * @return array<string,mixed>
     */
    private function csvQuery(array $query): array
    {
        foreach ($query as $k => $v) {
            if (is_array($v)) {
                $query[$k] = implode(',', $v);
            }
        }

        return $query;
    }
}
