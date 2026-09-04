<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Product Fees API (v0) — estimativa de tarifas (referral, FBA, etc.) pra
 * um preco hipotetico. Toda resposta vem em `payload.FeesEstimateResult`
 * (ou `payload[]` no batch).
 */
class ProductFees
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Estimativa de tarifas por SKU proprio
     * (POST /products/fees/v0/listings/{SellerSKU}/feesEstimate). Rate limit
     * 1 req/s, burst 2. Usa as medidas reais do item (so' depois de enviado
     * a Amazon). Resposta em `payload.FeesEstimateResult`.
     *
     * @param  array<string,mixed>  $feesEstimateRequest  FeesEstimateRequest (MarketplaceId, PriceToEstimateFees, Identifier, IsAmazonFulfilled…)
     */
    public function getMyFeesEstimateForSKU(string $sellerSku, array $feesEstimateRequest): array
    {
        return $this->client->post(
            '/products/fees/v0/listings/'.rawurlencode($sellerSku).'/feesEstimate',
            ['FeesEstimateRequest' => $feesEstimateRequest],
        );
    }

    /**
     * Estimativa de tarifas por ASIN
     * (POST /products/fees/v0/items/{Asin}/feesEstimate). Rate limit 1 req/s,
     * burst 2. Resposta em `payload.FeesEstimateResult`.
     *
     * @param  array<string,mixed>  $feesEstimateRequest  FeesEstimateRequest
     */
    public function getMyFeesEstimateForASIN(string $asin, array $feesEstimateRequest): array
    {
        return $this->client->post(
            '/products/fees/v0/items/'.rawurlencode($asin).'/feesEstimate',
            ['FeesEstimateRequest' => $feesEstimateRequest],
        );
    }

    /**
     * Estimativa em lote (ate 20 itens) (POST /products/fees/v0/feesEstimate).
     * Rate limit 0.5 req/s, burst 1. Cada item: `{FeesEstimateRequest:{…},
     * IdType:'ASIN'|'SellerSKU', IdValue}`. Resposta: lista de
     * FeesEstimateResult na raiz (`[]`).
     *
     * @param  array<int,array<string,mixed>>  $items  GetMyFeesEstimatesRequest (lista de FeesEstimateByIdRequest)
     */
    public function getMyFeesEstimates(array $items): array
    {
        return $this->client->post('/products/fees/v0/feesEstimate', $items);
    }
}
