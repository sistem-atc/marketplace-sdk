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

        return PromotionListResponseDTO::fromArray([
            'results' => $response['results'] ?? [],
            'paging'  => $response['paging'] ?? ['total' => 0, 'limit' => $limit, 'offset' => $offset],
        ]);
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
     * @param  array<string,mixed>  $filters  status / item_id / status_item
     */
    public function listItems(
        string $promotionId,
        string $promotionType,
        int $limit = 50,
        ?string $searchAfter = null,
        array $filters = [],
    ): PromotionListResponseDTO {
        // $filters opcionais da doc: status (candidate|started|...), item_id,
        // status_item (active|paused...).
        $query = array_merge($filters, [
            'app_version' => self::APP_VERSION,
            'promotion_type' => $promotionType,
            'limit' => min($limit, 50),
        ]);
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
    public function enrollItem(string $itemId, string $promotionId, string $promotionType, ?float $dealPrice = null, ?string $offerId = null): array
    {
        $body = [
            'promotion_id' => $promotionId,
            'promotion_type' => $promotionType,
        ];
        if ($dealPrice !== null) {
            $body['deal_price'] = $dealPrice;
        }
        if ($offerId !== null) {
            // Campanhas BANK (co-participacao PIX) exigem o offer_id do candidato.
            $body['offer_id'] = $offerId;
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
    public function withdrawItem(string $itemId, string $promotionId, string $promotionType, ?string $offerId = null): array
    {
        $query = [
            'app_version' => self::APP_VERSION,
            'promotion_id' => $promotionId,
            'promotion_type' => $promotionType,
        ];
        if ($offerId !== null) {
            // VOLUME e BANK pedem tambem o offer_id.
            $query['offer_id'] = $offerId;
        }

        return $this->makeRequest(HttpMethod::DELETE, "/seller-promotions/items/{$itemId}", $query);
    }

    /**
     * Remove o ITEM de TODAS as promocoes de uma vez (exclusao em massa).
     * Nao vale pra DOD e LIGHTNING — essas continuam uma por vez via
     * withdrawItem(). Resposta `{successful_ids: [{offer_id, error}], ...}`.
     *
     * @return array<string, mixed>
     */
    public function withdrawItemFromAll(string $itemId): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            "/seller-promotions/items/{$itemId}",
            ['app_version' => self::APP_VERSION],
        );
    }

    // ── Candidatos / ofertas / promocoes (doc "Gerenciar promocoes") ──────

    /**
     * Candidato (item convidado) de uma campanha: `CANDIDATE-{MLB}-{n}` — e o
     * resource do webhook public_candidates. Traz item_id, promotion_id, type,
     * status, precos sugeridos e prazo.
     *
     * @return array<string, mixed>
     */
    public function getCandidate(string $candidateId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/seller-promotions/candidates/'.rawurlencode($candidateId),
            ['app_version' => self::APP_VERSION],
        );
    }

    /**
     * Oferta ativa/inscrita: `OFFER-{MLB}-{n}` — resource do webhook
     * public_offers. Traz item_id, promotion_id, type, status, price,
     * original_price, meli/seller_percentage.
     *
     * @return array<string, mixed>
     */
    public function getOffer(string $offerId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/seller-promotions/offers/'.rawurlencode($offerId),
            ['app_version' => self::APP_VERSION],
        );
    }

    /**
     * Detalhe de uma promocao/campanha pelo path correto
     * `/seller-promotions/promotions/{id}?promotion_type=...` — o `get()` acima
     * (sem /promotions/ e sem promotion_type) responde 404. `promotionType`
     * e OBRIGATORIO: DEAL, VOLUME, BANK, SMART, PRE_NEGOTIATED, LIGHTNING...
     *
     * @return array<string, mixed>
     */
    public function getPromotion(string $promotionId, string $promotionType): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/seller-promotions/promotions/'.rawurlencode($promotionId),
            ['app_version' => self::APP_VERSION, 'promotion_type' => $promotionType],
        );
    }

    /**
     * Cria campanha do VENDEDOR (hoje: desconto por quantidade,
     * promotion_type=VOLUME). Body: promotion_type, sub_type (BNGM: buy_quantity
     * + pay_quantity | BNSP/SPONTH: buy_quantity + discount_percentage),
     * allow_combination, name, start_date, finish_date. Devolve a campanha
     * com id `C-MLBxxxx` e status pending. `$extraQuery` aceita
     * `version=test` (sandbox da doc).
     *
     * @param  array<string,mixed>  $body
     * @param  array<string,mixed>  $extraQuery
     * @return array<string, mixed>
     */
    public function createPromotion(array $body, array $extraQuery = []): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/seller-promotions/promotions',
            array_merge(['app_version' => self::APP_VERSION], $extraQuery),
            $body,
        );
    }

    /**
     * Atualiza campanha do vendedor (mesmo body do createPromotion()).
     *
     * @param  array<string,mixed>  $body
     * @param  array<string,mixed>  $extraQuery
     * @return array<string, mixed>
     */
    public function updatePromotion(string $promotionId, array $body, array $extraQuery = []): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/seller-promotions/promotions/'.rawurlencode($promotionId),
            array_merge(['app_version' => self::APP_VERSION], $extraQuery),
            $body,
        );
    }

    /**
     * Apaga campanha do vendedor. 200 com corpo null quando da certo.
     *
     * @return array<string, mixed>
     */
    public function deletePromotion(string $promotionId, string $promotionType = 'VOLUME'): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            '/seller-promotions/promotions/'.rawurlencode($promotionId),
            ['app_version' => self::APP_VERSION, 'promotion_type' => $promotionType],
        );
    }

    // ── Lista de exclusao (nao receber convites automaticos) ──────────────

    /**
     * Status de exclusao do SELLER: `{excluded: excluded|not_excluded}`.
     *
     * @return array<string, mixed>
     */
    public function sellerExclusionStatus(): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/seller-promotions/exclusion-list/seller',
            ['app_version' => self::APP_VERSION],
        );
    }

    /**
     * Inclui/remove o SELLER inteiro da lista de exclusao. O ML espera a
     * string "true"/"false" em `exclusion_status`.
     *
     * @return array<string, mixed>
     */
    public function setSellerExclusion(bool $excluded): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/seller-promotions/exclusion-list/seller',
            ['app_version' => self::APP_VERSION],
            ['exclusion_status' => $excluded ? 'true' : 'false'],
        );
    }

    /**
     * Status de exclusao de UM ITEM (path e /exclusion-list/seller/{item_id}).
     *
     * @return array<string, mixed>
     */
    public function itemExclusionStatus(string $itemId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/seller-promotions/exclusion-list/seller/{$itemId}",
            ['app_version' => self::APP_VERSION],
        );
    }

    /**
     * Inclui/remove UM ITEM da lista de exclusao.
     *
     * @return array<string, mixed>
     */
    public function setItemExclusion(string $itemId, bool $excluded): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/seller-promotions/exclusion-list/item',
            ['app_version' => self::APP_VERSION],
            ['item_id' => $itemId, 'exclusion_status' => $excluded ? 'true' : 'false'],
        );
    }
}
