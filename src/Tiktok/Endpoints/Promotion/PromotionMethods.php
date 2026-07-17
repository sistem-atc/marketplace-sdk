<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Promotion;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\Activity;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\ActivitySearchResponseDTO;

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
     */
    public function getActivities(
        int $pageSize = self::MAX_PAGE_SIZE,
        string $pageToken = '',
        ?string $status = null,
        string $version = self::DEFAULT_VERSION,
    ): ActivitySearchResponseDTO {
        // Listagem é o sub-recurso /search (POST) — o POST em /activities (sem
        // /search) é o CREATE (exige title). page_size/page_token vão no BODY
        // como tipos nativos (na query virariam string e o TikTok rejeita
        // "page_size type invalid, expected int32").
        $body = [
            'page_size' => min($pageSize, self::MAX_PAGE_SIZE),
        ];
        if ($pageToken !== '') {
            $body['page_token'] = $pageToken;
        }
        if ($status !== null) {
            $body['status'] = $status;
        }

        $response = $this->makeRequest(HttpMethod::POST, "/promotion/{$version}/activities/search", [], $body);

        return ActivitySearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Detalhe de uma activity (inclui produtos/itens participantes). */
    public function getActivity(string $activityId, string $version = self::DEFAULT_VERSION): Activity
    {
        $response = $this->makeRequest(HttpMethod::GET, "/promotion/{$version}/activities/{$activityId}");

        return Activity::fromArray($response['data'] ?? []);
    }
}
