<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Refund;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Reembolsos de pedido (Admin API REST — `refund`).
 */
class RefundMethods extends BaseMethods
{
    /**
     * Lista reembolsos de um pedido.
     *
     * @param  array<string, mixed>  $params  ex.: limit, fields, in_shop_currency
     */
    public function list(int|string $orderId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/refunds", $params);
    }

    /**
     * Recupera um reembolso do pedido.
     *
     * @param  array<string, mixed>  $params  ex.: fields, in_shop_currency
     */
    public function get(int|string $orderId, int|string $refundId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/refunds/{$refundId}", $params);
    }

    /**
     * Calcula um reembolso sem efetivar (sugere transactions e valores).
     * Ex.: ['shipping' => ['full_refund' => true], 'refund_line_items' => [['line_item_id' => 1, 'quantity' => 1, 'restock_type' => 'no_restock']]].
     *
     * @param  array<string, mixed>  $refund
     */
    public function calculate(int|string $orderId, array $refund): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/refunds/calculate", [], ['refund' => $refund]);
    }

    /**
     * Efetiva um reembolso (normalmente com as transactions vindas do calculate).
     *
     * @param  array<string, mixed>  $refund
     */
    public function create(int|string $orderId, array $refund): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/refunds", [], ['refund' => $refund]);
    }
}
