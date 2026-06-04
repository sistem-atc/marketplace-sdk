<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Promotion;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Seller Promotions API — campanhas e promoções do seller no ML.
 *
 * Endpoint base: GET /seller-promotions/users/{sellerId}
 * Versão obrigatória: app_version=v2 (v1 e v3 retornam 400 Bad Request).
 *
 * Tipos de promoção retornados:
 *   - DEAL               → Campanhas de desconto com data fixa (ex: 06.06)
 *   - LIGHTNING          → Oferta relâmpago
 *   - PRE_NEGOTIATED     → PA Favorabilidade / acordos pré-negociados
 *   - PRICE_MATCHING     → Saia na frente da concorrência
 *   - SMART              → Campanhas automáticas ML
 *   - SELLER_COUPON_CAMPAIGN → Cupons do vendedor (FIXED_AMOUNT / PERCENTAGE)
 *   - SELLER_CAMPAIGN    → Campanhas personalizadas do vendedor
 *
 * Campos de resposta: id, type, status, name, start_date, finish_date,
 *   deadline_date, sub_type?, fixed_amount?, min_purchase_amount?
 */
class PromotionMethods extends BaseMethods
{
    private const APP_VERSION = 'v2';

    /**
     * Lista as promoções/campanhas de um seller.
     *
     * @param  string|null  $status  Filtra por status: 'started'|'paused'|'finished'|'candidate'
     * @param  string|null  $type    Filtra por tipo: 'DEAL'|'LIGHTNING'|'PRE_NEGOTIATED'|...
     * @return array{results: array<int, array<string, mixed>>, paging: array<string, int>}
     */
    public function list(
        int|string $sellerId,
        ?string $status = null,
        ?string $type = null,
        int $limit = 50,
        int $offset = 0,
    ): array {
        $query = [
            'app_version' => self::APP_VERSION,
            'limit'       => min($limit, 50),
            'offset'      => $offset,
        ];

        if ($status !== null) {
            $query['status'] = $status;
        }

        if ($type !== null) {
            $query['type'] = $type;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/seller-promotions/users/{$sellerId}",
            $query,
        );

        return [
            'results' => $response['results'] ?? [],
            'paging'  => $response['paging'] ?? ['total' => 0, 'limit' => $limit, 'offset' => $offset],
        ];
    }

    /**
     * Itera todas as páginas de promoções de um seller.
     * Útil para backfill ou sincronização completa.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listAll(
        int|string $sellerId,
        ?string $status = null,
        ?string $type = null,
    ): array {
        $all    = [];
        $offset = 0;
        $limit  = 50;

        do {
            $page   = $this->list($sellerId, $status, $type, $limit, $offset);
            $results = $page['results'];
            $all    = array_merge($all, $results);
            $total  = $page['paging']['total'] ?? 0;
            $offset += $limit;
        } while (count($results) === $limit && $offset < $total);

        return $all;
    }
}
