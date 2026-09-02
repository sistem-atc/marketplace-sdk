<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Metrics;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Metricas e Tendencias — visitas, tendencias de busca, mais vendidos,
 * opinioes, qualidade de publicacao, experiencia de compra, programa
 * Decola/Beneficio de Reputacao e qualidade de catalogo (carga de atributos).
 *
 * Todos devolvem array cru (sem DTO nesta rodada).
 */
class MetricsMethods extends BaseMethods
{
    // ── Visitas ──────────────────────────────────────────────────────────

    /**
     * Total de visitas de TODOS os anuncios do usuario na faixa de datas
     * (YYYY-MM-DD). Resposta `{user_id, total_visits, visits_detail: [...]}`.
     */
    public function userItemsVisits(int|string $userId, string $dateFrom, string $dateTo): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/items_visits", [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }

    /**
     * Visitas dos anuncios do usuario numa janela: `last` (quantidade de
     * unidades), `unit` (day|hour), `ending` (data final, opcional — default
     * hoje). Vem detalhado por intervalo em `results[]`.
     */
    public function userItemsVisitsTimeWindow(int|string $userId, int $last, string $unit = 'day', ?string $ending = null): array
    {
        $query = ['last' => $last, 'unit' => $unit];
        if ($ending !== null) {
            $query['ending'] = $ending;
        }

        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/items_visits/time_window", $query);
    }

    /**
     * Visitas de um conjunto de anuncios na faixa de datas. Resposta e lista
     * `[{item_id, date_from, date_to, total_visits, visits_detail}]`.
     *
     * @param  list<string>  $itemIds
     */
    public function itemsVisits(array $itemIds, string $dateFrom, string $dateTo): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/visits', [
            'ids' => implode(',', $itemIds),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }

    /**
     * Visitas de UM anuncio numa janela (`last`, `unit` day|hour, `ending`).
     */
    public function itemVisitsTimeWindow(string $itemId, int $last, string $unit = 'day', ?string $ending = null): array
    {
        $query = ['last' => $last, 'unit' => $unit];
        if ($ending !== null) {
            $query['ending'] = $ending;
        }

        return $this->makeRequest(HttpMethod::GET, "/items/{$itemId}/visits/time_window", $query);
    }

    /**
     * Visitas TOTAIS (ultimos 2 anos) por anuncio: `{MLB123: 552, ...}`.
     *
     * @param  list<string>  $itemIds
     */
    public function visitsTotal(array $itemIds): array
    {
        return $this->makeRequest(HttpMethod::GET, '/visits/items', ['ids' => implode(',', $itemIds)]);
    }

    // ── Tendencias / mais vendidos ───────────────────────────────────────

    /**
     * Palavras mais buscadas no site (`[{keyword, url}]`); com `categoryId`
     * restringe a categoria.
     */
    public function trends(string $siteId, ?string $categoryId = null): array
    {
        $path = "/trends/{$siteId}".($categoryId !== null ? "/{$categoryId}" : '');

        return $this->makeRequest(HttpMethod::GET, $path);
    }

    /**
     * Top 20 mais vendidos de uma categoria (`content[]` mistura ITEM,
     * PRODUCT e USER_PRODUCT). `attribute`+`attributeValue` (ex.: BRAND +
     * id da marca) filtram por atributo — os dois juntos.
     */
    public function highlightsByCategory(string $siteId, string $categoryId, ?string $attribute = null, ?string $attributeValue = null): array
    {
        $query = [];
        if ($attribute !== null && $attributeValue !== null) {
            $query = ['attribute' => $attribute, 'attributeValue' => $attributeValue];
        }

        return $this->makeRequest(HttpMethod::GET, "/highlights/{$siteId}/category/{$categoryId}", $query);
    }

    /** Posicao de um ANUNCIO no ranking de mais vendidos (`{dimension, position, ...}`). */
    public function highlightsForItem(string $siteId, string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/highlights/{$siteId}/item/{$itemId}");
    }

    /** Posicao de um PRODUTO de catalogo no ranking de mais vendidos. */
    public function highlightsForProduct(string $siteId, string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/highlights/{$siteId}/product/{$productId}");
    }

    // ── Opinioes / qualidade ─────────────────────────────────────────────

    /**
     * Avaliacoes de um anuncio: `{paging, reviews[], rating_average,
     * rating_levels}`. Query: offset, limit (default 5), catalog_product_id
     * (avaliacoes do produto de catalogo). Item pausado pode dar 404/vazio.
     *
     * @param  array<string,mixed>  $query
     */
    public function reviews(string $itemId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/reviews/item/{$itemId}", $query);
    }

    /**
     * Qualidade da publicacao (substitui /health): `{entity_type: ITEM,
     * level, score, actions[]...}`. Path no SINGULAR `/item/`.
     */
    public function itemPerformance(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/item/{$itemId}/performance");
    }

    /** Qualidade de um user product (id MLBU...). */
    public function userProductPerformance(string $userProductId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/user-product/{$userProductId}/performance");
    }

    // ── Experiencia de compra (reputacao por item) ───────────────────────

    /**
     * Experiencia de compra do anuncio (reclamacoes, cancelamentos, atrasos
     * que pesam na reputacao). `locale` ex.: pt_BR.
     */
    public function itemPurchaseExperience(string $itemId, ?string $locale = null): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/reputation/items/{$itemId}/purchase_experience/integrators",
            $locale !== null ? ['locale' => $locale] : [],
        );
    }

    /**
     * Experiencia de compra de um user product. A doc alterna
     * `/user_products/` e `/users_products/` — usamos o da "Chamada" oficial
     * (`user_products`).
     */
    public function userProductPurchaseExperience(string $userProductId, ?string $locale = null): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/reputation/user_products/{$userProductId}/purchase_experience/integrators",
            $locale !== null ? ['locale' => $locale] : [],
        );
    }

    // ── Programa Decola / Beneficio de Reputacao (seller_recovery) ────────

    /**
     * Status do programa pro seller do token: `{seller_id, current_level,
     * program, status, guarantee_amount, ...}`. Limite 100 RPM por seller.
     */
    public function sellerRecoveryStatus(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/users/reputation/seller_recovery/status');
    }

    /**
     * Ativa (opt-in) o programa — o valor da garantia precisa estar
     * disponivel/reservado no Mercado Pago, senao erro.
     */
    public function activateSellerRecovery(): array
    {
        return $this->makeRequest(HttpMethod::POST, '/users/reputation/seller_recovery/activate');
    }

    /**
     * Cancela a garantia. `cancellationReason` obrigatorio no Decola
     * (business_not_ready | program_not_useful | need_money | goal_achieved |
     * without_reason); dispensavel no Beneficio de Reputacao.
     */
    public function cancelSellerRecoveryGuarantee(?string $cancellationReason = null): array
    {
        $body = $cancellationReason !== null ? ['cancellation_reason' => $cancellationReason] : [];

        return $this->makeRequest(HttpMethod::PUT, '/users/reputation/seller_recovery/cancel_guarantee', [], $body);
    }

    /**
     * Termo legal em base64 (`{document}`): `PREVIEW` antes de ativar,
     * `COMPLETE` so com protecao ACTIVE ou FINISHED_BY_*.
     */
    public function sellerRecoveryLegalDocument(string $type = 'PREVIEW'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/users/reputation/seller_recovery/legal-document', ['type' => $type]);
    }

    // ── Qualidade de catalogo (carga de atributos) ───────────────────────

    /**
     * Metricas de completude de atributos do SELLER (so ate 80k itens).
     * `includeItems=true` lista os itens com pendencias. `version` e o `v`
     * da doc (32 hoje).
     */
    public function catalogQualityStatusForSeller(int|string $sellerId, bool $includeItems = false, string $version = '32'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog_quality/status', [
            'seller_id' => $sellerId,
            'include_items' => $includeItems ? 'true' : 'false',
            'v' => $version,
        ]);
    }

    /** Integridade/qualidade de UM anuncio (`v=3`). */
    public function catalogQualityStatusForItem(string $itemId, string $version = '3'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog_quality/status', [
            'item_id' => $itemId,
            'v' => $version,
        ]);
    }
}
