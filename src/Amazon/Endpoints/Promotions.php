<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Promotions API (2025-12-01) — consulta (somente leitura) das promocoes
 * do seller (Best Deal, Lightning Deal, Price Discount, Coupon…) e das
 * selecoes de ASIN/SKU de cada uma.
 */
class Promotions
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista promocoes (GET /promotions/2025-12-01/promotions). Filtros:
     * statuses, asins, skus, promotionTypes, startDateAfter/Before, endDateAfter/Before, updateDateAfter/Before,
     * revision, includedData; `limit` + paginacao por `paginationToken`
     * (resposta traz `pagination.nextToken`). Arrays em CSV. Resposta:
     * `promotions[]`.
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query
     */
    public function searchPromotions(array|string $marketplaceIds, array $query = []): array
    {
        return $this->client->get('/promotions/2025-12-01/promotions', $this->csvQuery(['marketplaceIds' => $marketplaceIds] + $query));
    }

    /**
     * Detalhe de uma promocao (GET /promotions/2025-12-01/promotions/{promotionId}).
     *
     * @param  array<string,mixed>  $query  includedData, locale
     */
    public function getPromotion(string $promotionId, array $query = []): array
    {
        return $this->client->get('/promotions/2025-12-01/promotions/'.rawurlencode($promotionId), $this->csvQuery($query));
    }

    /**
     * Itens de uma selecao da promocao
     * (GET /promotions/2025-12-01/promotions/{promotionId}/selections/{selectionId}).
     * `revisionId` obrigatorio. `limit` + paginacao por `paginationToken`.
     *
     * @param  array<string,mixed>  $query  locale, paginationToken, limit, includedData
     */
    public function getSelection(string $promotionId, string $selectionId, int $revisionId, array $query = []): array
    {
        return $this->client->get(
            '/promotions/2025-12-01/promotions/'.rawurlencode($promotionId).'/selections/'.rawurlencode($selectionId),
            $this->csvQuery(['revisionId' => $revisionId] + $query),
        );
    }

    /**
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
