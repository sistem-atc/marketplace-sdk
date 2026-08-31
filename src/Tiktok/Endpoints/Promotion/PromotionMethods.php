<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Promotion;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\Activity;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\ActivitySearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\Coupon;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\CouponSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\RepublishActivityResponseDTO;

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

    /** Cupom vive numa versão própria da API — não é a 202309 das activities. */
    private const COUPON_VERSION = '202406';

    /** Republicar activity tem versão própria (202607); não existe na 202309. */
    private const REPUBLISH_VERSION = '202607';

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
    /**
     * Detalhe de um cupom (`data.coupon`).
     *
     * Cupom NÃO é Activity: mora numa versão própria da API (202406) e é o
     * desconto que o comprador resgata e aplica no checkout, enquanto a
     * Activity mexe no preço do anúncio. Só aqui vêm `usageStats` (resgates x
     * usos), `productIds` e as tarefas de LIVE.
     */
    public function getCoupon(string $couponId, string $version = self::COUPON_VERSION): Coupon
    {
        $response = $this->makeRequest(HttpMethod::GET, "/promotion/{$version}/coupons/{$couponId}");

        return Coupon::fromArray($response['data']['coupon'] ?? []);
    }

    /**
     * Lista/busca cupons, paginada por cursor (`page_token`).
     *
     * Diferente do activities/search, aqui page_size e page_token vão na QUERY
     * (é o que a doc especifica) e os filtros no BODY. `status` e
     * `displayType` são LISTAS de string, não string.
     *
     * A página traz um subconjunto do cupom — sem usage_stats/product_ids;
     * pra esses, chame getCoupon() no id.
     *
     * @param  list<string>|null  $status  NOT_START | ONGOING | EXPIRED | DEACTIVATED
     * @param  list<string>|null  $displayType  REGULAR | LIVE | CREATOR_EXCLUSIVE | CHAT | PROMO_CODE
     */
    public function searchCoupons(
        int $pageSize = 50,
        string $pageToken = '',
        ?array $status = null,
        ?string $titleKeyword = null,
        ?array $displayType = null,
        string $version = self::COUPON_VERSION,
    ): CouponSearchResponseDTO {
        $query = ['page_size' => min($pageSize, self::MAX_PAGE_SIZE)];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = [];
        if ($status !== null) {
            $body['status'] = $status;
        }
        if ($titleKeyword !== null) {
            $body['title_keyword'] = $titleKeyword;
        }
        if ($displayType !== null) {
            $body['display_type'] = $displayType;
        }

        $response = $this->makeRequest(HttpMethod::POST, "/promotion/{$version}/coupons/search", $query, $body);

        return CouponSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Republica uma activity encerrada/expirada
     * (`RepublishActivityResponseDTO`).
     *
     * NAO reativa a campanha antiga: cria uma NOVA a partir dela e devolve o
     * id da nova em `activityId`. Quem guardar o id antigo fica monitorando
     * uma campanha que nao roda mais.
     *
     * Versao propria (202607) — nao e' a 202309 do resto das activities.
     * Nem toda campanha aceita (erro 17003243).
     *
     * `$beginTime`/`$endTime` sao epoch em SEGUNDOS e opcionais (sem eles a
     * API reaproveita a janela da campanha de origem). A doc exige inicio no
     * FUTURO.
     */
    public function republishActivity(
        string $activityId,
        ?int $beginTime = null,
        ?int $endTime = null,
        string $version = self::REPUBLISH_VERSION,
    ): RepublishActivityResponseDTO {
        $body = array_filter([
            'begin_time' => $beginTime,
            'end_time' => $endTime,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/promotion/{$version}/activities/{$activityId}/republish",
            [],
            $body,
        );

        return RepublishActivityResponseDTO::fromArray($response['data'] ?? []);
    }
}
