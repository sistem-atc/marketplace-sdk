<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Replenishment API v2022-11-07 (Subscribe & Save — métricas e ofertas).
 *
 * Path base: /replenishment/2022-11-07. Todas as operações são POST de busca;
 * rate limit do modelo: 1 req/s, burst 1. Respostas sem envelope `payload`;
 * as listas paginam por `pagination.totalResults` + `pagination.pageNumber`
 * no body da request.
 */
class Replenishment
{
    private const BASE = '/replenishment/2022-11-07';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Métricas agregadas do seller (receita, unidades, assinaturas). POST /sellingPartners/metrics/search — 1 req/s.
     * Dado em `metrics`.
     *
     * @param  array<string, mixed>  $body  GetSellingPartnerMetricsRequest (aggregationFrequency, timeInterval, metrics, timePeriodType, marketplaceId, programTypes)
     */
    public function getSellingPartnerMetrics(array $body = []): array
    {
        return $this->client->post(self::BASE.'/sellingPartners/metrics/search', $body);
    }

    /**
     * Métricas por oferta (ASIN/SKU). POST /offers/metrics/search — 1 req/s. Dado em `offers`.
     *
     * @param  array<string, mixed>  $body  ListOfferMetricsRequest (pagination, sort, filters)
     */
    public function listOfferMetrics(array $body = []): array
    {
        return $this->client->post(self::BASE.'/offers/metrics/search', $body);
    }

    /**
     * Lista ofertas inscritas no programa. POST /offers/search — 1 req/s. Dado em `offers`.
     *
     * @param  array<string, mixed>  $body  ListOffersRequest (pagination, filters, sort)
     */
    public function listOffers(array $body = []): array
    {
        return $this->client->post(self::BASE.'/offers/search', $body);
    }
}
