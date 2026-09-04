<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Order;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Order Risk — avaliacoes de fraude de um pedido (`orders/{order_id}/risks`).
 * Deprecado na Shopify a partir de 2024-04 em favor de `orderRiskAssessment`
 * (GraphQL); ainda responde no REST.
 */
class OrderRiskMethods extends BaseMethods
{
    /**
     * Lista os riscos de um pedido.
     */
    public function list(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/risks");
    }

    /**
     * Recupera um risco pelo ID.
     */
    public function get(int|string $orderId, int|string $riskId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/risks/{$riskId}");
    }

    /**
     * Cria um risco. Embrulha em `risk`.
     *
     * @param  array<string, mixed>  $risk
     */
    public function create(int|string $orderId, array $risk): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/risks", [], ['risk' => $risk]);
    }

    /**
     * Atualiza um risco. Embrulha em `risk`.
     *
     * @param  array<string, mixed>  $risk
     */
    public function update(int|string $orderId, int|string $riskId, array $risk): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/orders/{$orderId}/risks/{$riskId}", [], ['risk' => $risk]);
    }

    /**
     * Exclui um risco.
     */
    public function delete(int|string $orderId, int|string $riskId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/orders/{$orderId}/risks/{$riskId}");
    }
}
