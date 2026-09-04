<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Promotion;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Promocoes do canal (GET/POST /seller/v1/promotions).
 *
 * Fluxo: listar elegiveis → subscribe() na promocao → ajustar SKUs
 * (updateSku/removeSku) → apply() pra publicar alteracoes pendentes.
 * Precos em centavos com `normalizer`. Paginacao `_limit`/`_offset`/`_sort`
 * (default created_at:desc). Escopo open:promotions-seller:read|write.
 */
class PromotionMethods extends BaseMethods
{
    /**
     * Promocoes elegiveis/inscritas (GET /seller/v1/promotions).
     *
     * Filtros: `type` (percentage_discount|absolute_discount|coupon_discount|
     * fidelity_discount|freight_discount), `origin` (channel|self_service),
     * `start_at__gte|lte`, `end_at__gte|lte`, `subscription_deadline__gte|lte`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, '/seller/v1/promotions', $query);
    }

    /**
     * Detalhe da promocao (GET /seller/v1/promotions/{id}).
     *
     * @return array<string, mixed>
     */
    public function get(string $promotionId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/promotions/{$promotionId}");
    }

    /**
     * Publica as alteracoes pendentes da promocao (POST /seller/v1/promotions/{id}/apply).
     *
     * @return array<string, mixed>
     */
    public function apply(string $promotionId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/seller/v1/promotions/{$promotionId}/apply");
    }

    /**
     * SKUs do seller na promocao (GET /seller/v1/promotions/{id}/skus).
     *
     * @return array<string, mixed>
     */
    public function listSkus(string $promotionId, int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, "/seller/v1/promotions/{$promotionId}/skus", $query);
    }

    /**
     * Detalhe de um SKU na promocao (GET /seller/v1/promotions/{id}/skus/{sku}).
     *
     * @return array<string, mixed>
     */
    public function getSku(string $promotionId, string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/promotions/{$promotionId}/skus/".rawurlencode($sku));
    }

    /**
     * Ajusta preco promocional e/ou limite de estoque do SKU
     * (PATCH /seller/v1/promotions/{id}/skus/{sku}).
     * `promotionalPrice` em centavos (normalizer 100).
     *
     * @return array<string, mixed>
     */
    public function updateSku(
        string $promotionId,
        string $sku,
        string $channelId,
        ?int $promotionalPrice = null,
        ?int $inventoryLimit = null,
        string $currency = 'BRL',
        int $normalizer = 100,
    ): array {
        $body = ['channel' => ['id' => $channelId]];
        if ($promotionalPrice !== null) {
            $body['price'] = ['promotional' => ['value' => $promotionalPrice, 'currency' => $currency, 'normalizer' => $normalizer]];
        }
        if ($inventoryLimit !== null) $body['inventory'] = ['limit' => $inventoryLimit];

        return $this->makeRequest(HttpMethod::PATCH, "/seller/v1/promotions/{$promotionId}/skus/".rawurlencode($sku), [], $body);
    }

    /**
     * Remove o SKU da promocao (DELETE /seller/v1/promotions/{id}/skus/{sku}).
     *
     * @return array<string, mixed>
     */
    public function removeSku(string $promotionId, string $sku): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/seller/v1/promotions/{$promotionId}/skus/".rawurlencode($sku));
    }

    /**
     * Inscreve o seller na promocao (POST /seller/v1/promotions/{id}/subscriptions).
     * `limit` = teto de unidades na campanha (opcional).
     *
     * @return array<string, mixed>
     */
    public function subscribe(string $promotionId, string $channelId, ?int $limit = null): array
    {
        $body = ['channel' => ['id' => $channelId]];
        if ($limit !== null) $body['limit'] = $limit;

        return $this->makeRequest(HttpMethod::POST, "/seller/v1/promotions/{$promotionId}/subscriptions", [], $body);
    }

    /**
     * Cancela a inscricao (DELETE /seller/v1/promotions/{id}/subscriptions).
     *
     * @return array<string, mixed>
     */
    public function unsubscribe(string $promotionId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/seller/v1/promotions/{$promotionId}/subscriptions");
    }
}
