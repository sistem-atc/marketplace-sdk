<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Promotion;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * Endpoints de promoção/campanha do TikTok Shop (OpenAPI /promotion/{version}).
 *
 * Pagination: cursor via `page_token`; `next_page_token` na resposta indica se
 * há mais. Activity = campanha (FLASHSALE / DIRECT_DISCOUNT, etc).
 */
class PromotionMethods extends BaseMethods
{
    private const DEFAULT_VERSION = '202309';

    private const MAX_PAGE_SIZE = 100;

    /**
     * Lista as activities (campanhas/promoções) da loja, paginada por page_token.
     *
     * @param  string|null  $status  ONGOING | NOT_START | EXPIRED | DEACTIVATED (omitido = todas)
     * @return array{activities: array<int, array<string, mixed>>, next_page_token: string|null, total_count: int|null}
     */
    public function getActivities(
        int $pageSize = self::MAX_PAGE_SIZE,
        string $pageToken = '',
        ?string $status = null,
        string $version = self::DEFAULT_VERSION,
    ): array {
        // TikTok lista via POST (page_size/page_token na query, filtros no body),
        // igual /order/.../orders/search. GET retorna 36009010 "Invalid method".
        $query = [
            'page_size' => min($pageSize, self::MAX_PAGE_SIZE),
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = [];
        if ($status !== null) {
            $body['status'] = $status;
        }

        $response = $this->makeRequest(HttpMethod::POST, "/promotion/{$version}/activities", $query, $body);
        $data = $response['data'] ?? [];

        return [
            'activities' => $data['activities'] ?? [],
            'next_page_token' => $data['next_page_token'] ?? null,
            'total_count' => $data['total_count'] ?? null,
        ];
    }

    /**
     * Detalhe de uma activity (inclui produtos/itens participantes).
     *
     * @return array<string, mixed>
     */
    public function getActivity(string $activityId, string $version = self::DEFAULT_VERSION): array
    {
        return $this->makeRequest(HttpMethod::GET, "/promotion/{$version}/activities/{$activityId}");
    }
}
