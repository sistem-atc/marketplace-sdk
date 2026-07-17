<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Promotion;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Promotion\Promotion;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Promotion\PromotionListResponseDTO;

/**
 * Seller Promotions API — campanhas e promoções do seller no ML.
 *
 * Endpoint base: GET /seller-promotions/users/{sellerId}
 * Versão obrigatória: app_version=v2 (v1 e v3 retornam 400 Bad Request).
 *
 * Status dos endpoints (2026-06-04):
 *   list()    → 200 ✅ funcional
 *   listAll() → 200 ✅ funcional
 *   get()     → 404 🔒 aguardando liberação ML
 *   listItems() → 404 🔒 aguardando liberação ML
 *   getSubscription() → 404 🔒 aguardando liberação ML
 *   subscribe() → 404 🔒 aguardando liberação ML
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
     */
    public function list(
        int|string $sellerId,
        ?string $status = null,
        ?string $type = null,
        int $limit = 50,
        int $offset = 0,
    ): PromotionListResponseDTO {
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
     * @return list<Promotion>
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
            $page    = $this->list($sellerId, $status, $type, $limit, $offset);
            $results = $page->results;
            $all     = array_merge($all, $results);
            $total   = (int) (data_get($page->paging, 'total') ?? 0);
            $offset += $limit;
        } while (count($results) === $limit && $offset < $total);

        return $all;
    }

    // ── Endpoints aguardando liberação pelo ML (retornam 404 em 2026-06-04) ──
    // Implementados e prontos — quando o ML liberar basta usar normalmente.

    /**
     * Detalhe de uma campanha específica.
     * Aguardando: GET /seller-promotions/{promotionId}?app_version=v2
     */
    public function get(string $promotionId): Promotion
    {
        return Promotion::fromArray($this->makeRequest(
            HttpMethod::GET,
            "/seller-promotions/{$promotionId}",
            ['app_version' => self::APP_VERSION],
        ));
    }

    /**
     * Promoções/campanhas em que um ITEM (anúncio) participa ou é candidato.
     * Usado pra sync CIRÚRGICO a partir do webhook public_candidates/public_offers
     * (cujo resource traz o MLB) — busca só o item afetado em vez de listAll.
     *
     * GET /seller-promotions/items/{itemId}?app_version=v2
     *
     * Retorna LISTA (1 entry por campanha do item): cada uma traz id (= promotion_id),
     * type (SMART|DEAL|SELLER_COUPON_CAMPAIGN|...), ref_id (= resource do webhook),
     * status (candidate|started), price, original_price, meli_percentage,
     * seller_percentage, name, start_date/finish_date.
     *
     * @return list<Promotion>
     */
    public function getItemPromotions(string $itemId): array
    {
        return array_map(
            fn (array $row) => Promotion::fromArray($row),
            (array) $this->makeRequest(
                HttpMethod::GET,
                "/seller-promotions/items/{$itemId}",
                ['app_version' => self::APP_VERSION],
            ),
        );
    }

    /**
     * Items participantes de uma campanha.
     *
     * GET /seller-promotions/promotions/{promotionId}/items?promotion_type={TYPE}&app_version=v2
     *
     * IMPORTANTE: o path tem o segmento `/promotions/` e o `promotion_type` é
     * OBRIGATÓRIO — sem eles o ML responde 404 resource_not_found. Paginação é
     * por cursor `search_after` (o `paging.searchAfter` da resposta vira o
     * `search_after` da próxima página), não por offset.
     *
     * Cada item traz: id (MLB), status (candidate|started), price, original_price,
     * meli_percentage (subsídio ML), seller_percentage, offer_id, start/end_date.
     *
     * @param  string  $promotionType  SMART|DEAL|LIGHTNING|PRE_NEGOTIATED|...
     */
    public function listItems(
        string $promotionId,
        string $promotionType,
        int $limit = 50,
        ?string $searchAfter = null,
    ): PromotionListResponseDTO {
        $query = [
            'app_version' => self::APP_VERSION,
            'promotion_type' => $promotionType,
            'limit' => min($limit, 50),
        ];
        if ($searchAfter !== null && $searchAfter !== '') {
            $query['search_after'] = $searchAfter;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/seller-promotions/promotions/{$promotionId}/items",
            $query,
        );

        return PromotionListResponseDTO::fromArray([
            'results' => $response['results'] ?? [],
            'paging'  => $response['paging'] ?? [],
        ]);
    }

    /**
     * Subscription/opt-in status de uma campanha.
     * Aguardando: GET /seller-promotions/{promotionId}/subscription?app_version=v2
     *
     * @return array<string, mixed>
     */
    public function getSubscription(string $promotionId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/seller-promotions/{$promotionId}/subscription",
            ['app_version' => self::APP_VERSION],
        );
    }

    /**
     * Opt-in em uma campanha (aceitar convite/participar).
     * Aguardando: POST /seller-promotions/{promotionId}/subscription?app_version=v2
     *
     * @return array<string, mixed>
     */
    public function subscribe(string $promotionId): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/seller-promotions/{$promotionId}/subscription",
            ['app_version' => self::APP_VERSION],
        );
    }

    /**
     * Opt-out de uma campanha.
     * Aguardando: DELETE /seller-promotions/{promotionId}/subscription?app_version=v2
     *
     * @return array<string, mixed>
     */
    public function unsubscribe(string $promotionId): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            "/seller-promotions/{$promotionId}/subscription",
            ['app_version' => self::APP_VERSION],
        );
    }

    /**
     * Inclui um ITEM (anúncio) numa promoção — participar com preço.
     * POST /seller-promotions/items/{itemId}?app_version=v2
     * Body: { promotion_id, promotion_type, deal_price? }
     *
     * deal_price é obrigatório pros tipos com faixa (DEAL/LIGHTNING); pros
     * pré-negociados (SMART/PRE_NEGOTIATED) o preço já é definido pelo ML e
     * pode ser omitido (passe null).
     *
     * @return array<string, mixed>
     */
    public function enrollItem(string $itemId, string $promotionId, string $promotionType, ?float $dealPrice = null): array
    {
        $body = [
            'promotion_id' => $promotionId,
            'promotion_type' => $promotionType,
        ];
        if ($dealPrice !== null) {
            $body['deal_price'] = $dealPrice;
        }

        return $this->makeRequest(
            HttpMethod::POST,
            "/seller-promotions/items/{$itemId}",
            ['app_version' => self::APP_VERSION],
            $body,
        );
    }

    /**
     * Remove um ITEM de uma promoção (deixar de participar).
     * DELETE /seller-promotions/items/{itemId}?app_version=v2&promotion_id=&promotion_type=
     *
     * @return array<string, mixed>
     */
    public function withdrawItem(string $itemId, string $promotionId, string $promotionType): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            "/seller-promotions/items/{$itemId}",
            [
                'app_version' => self::APP_VERSION,
                'promotion_id' => $promotionId,
                'promotion_type' => $promotionType,
            ],
        );
    }
}
