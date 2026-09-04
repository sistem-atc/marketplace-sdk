<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Feedback do app pro lojista (Admin API REST — `resource_feedback` no nivel
 * da loja e `product_resource_feedback` por produto). Corpo:
 * ['state' => 'requires_action'|'success', 'messages' => [...], 'feedback_generated_at' => ISO8601, 'resource_updated_at' => ISO8601].
 */
class ResourceFeedbackMethods extends BaseMethods
{
    /**
     * Lista o feedback do app no nivel da loja.
     */
    public function list(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/resource_feedback');
    }

    /**
     * Cria/atualiza o feedback do app no nivel da loja.
     *
     * @param  array<string, mixed>  $feedback
     */
    public function create(array $feedback): array
    {
        return $this->makeRequest(HttpMethod::POST, '/resource_feedback', [], ['resource_feedback' => $feedback]);
    }

    /**
     * Lista o feedback do app sobre um produto.
     */
    public function listForProduct(int|string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/products/{$productId}/resource_feedback");
    }

    /**
     * Cria/atualiza o feedback do app sobre um produto.
     *
     * @param  array<string, mixed>  $feedback
     */
    public function createForProduct(int|string $productId, array $feedback): array
    {
        return $this->makeRequest(HttpMethod::POST, "/products/{$productId}/resource_feedback", [], ['resource_feedback' => $feedback]);
    }
}
