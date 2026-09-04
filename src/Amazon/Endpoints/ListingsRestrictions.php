<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Listings Restrictions API (2021-08-01) — diz se o seller pode listar um
 * ASIN (gating/brand approval) e o que precisa fazer pra liberar.
 */
class ListingsRestrictions
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Restricoes de listagem de um ASIN pro seller
     * (GET /listings/2021-08-01/restrictions). Rate limit 5 req/s, burst 10.
     * Opcional: `conditionType` (new_new, used_good…), `reasonLocale`,
     * `productType`. Resposta: `restrictions[]` (marketplaceId, conditionType,
     * reasons[] com links de aprovacao).
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query  conditionType, reasonLocale, productType
     */
    public function getListingsRestrictions(string $asin, string $sellerId, array|string $marketplaceIds, array $query = []): array
    {
        return $this->client->get('/listings/2021-08-01/restrictions', [
            'asin' => $asin,
            'sellerId' => $sellerId,
            'marketplaceIds' => is_array($marketplaceIds) ? implode(',', $marketplaceIds) : $marketplaceIds,
        ] + $query);
    }
}
