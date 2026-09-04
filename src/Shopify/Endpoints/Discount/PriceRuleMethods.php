<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Discount;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Regras de preco / descontos (Admin API REST — `price_rule`).
 * Os codigos de desconto (discount_code) sao filhos da price_rule.
 */
class PriceRuleMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista regras de preco (1 pagina, max 250).
     *
     * @param  array<string, mixed>  $params  ex.: limit, since_id, created_at_min/max, updated_at_min/max, starts_at_min/max, ends_at_min/max, times_used
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/price_rules', $params);
    }

    /**
     * Itera TODAS as regras de preco (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/price_rules', 'price_rules', $params, $limit);
    }

    /**
     * Conta regras de preco.
     */
    public function count(): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/price_rules/count');

        return $response['count'] ?? 0;
    }

    /**
     * Recupera uma regra de preco.
     */
    public function get(int|string $priceRuleId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/price_rules/{$priceRuleId}");
    }

    /**
     * Cria uma regra de preco (title, target_type, target_selection,
     * allocation_method, value_type, value, customer_selection, starts_at).
     *
     * @param  array<string, mixed>  $priceRule
     */
    public function create(array $priceRule): array
    {
        return $this->makeRequest(HttpMethod::POST, '/price_rules', [], ['price_rule' => $priceRule]);
    }

    /**
     * Atualiza uma regra de preco.
     *
     * @param  array<string, mixed>  $priceRule
     */
    public function update(int|string $priceRuleId, array $priceRule): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/price_rules/{$priceRuleId}", [], ['price_rule' => $priceRule]);
    }

    /**
     * Remove uma regra de preco.
     */
    public function delete(int|string $priceRuleId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/price_rules/{$priceRuleId}");
    }
}
