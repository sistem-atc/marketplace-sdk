<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Discount;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Codigos de desconto de uma price rule (inclui criacao em lote).
 *
 * Recurso REST: `discount_code` (a `price_rule` em si fica fora deste lote).
 * Obs.: em `lookup()` a Shopify responde 303 redirecionando pro codigo;
 * o client HTTP segue o redirect e devolve o JSON final.
 */
class DiscountCodeMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista os codigos de uma price rule.
     *
     * @param  array<string, mixed>  $params  limit
     */
    public function list(int|string $priceRuleId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/price_rules/{$priceRuleId}/discount_codes", $params);
    }

    /**
     * Itera todos os codigos de uma price rule (paginacao por cursor).
     *
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(int|string $priceRuleId, int $limit = 250): \Generator
    {
        return $this->eachPage("/price_rules/{$priceRuleId}/discount_codes.json", 'discount_codes', [], $limit);
    }

    /**
     * Total de codigos de desconto da loja.
     *
     * @param  array<string, mixed>  $params  times_used, times_used_min, times_used_max
     */
    public function count(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/discount_codes/count', $params);
    }

    /**
     * Recupera um codigo.
     */
    public function get(int|string $priceRuleId, int|string $discountCodeId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/price_rules/{$priceRuleId}/discount_codes/{$discountCodeId}");
    }

    /**
     * Localiza um codigo pelo texto (ex.: `SUMMERSALE10`), sem saber a price rule.
     */
    public function lookup(string $code): array
    {
        return $this->makeRequest(HttpMethod::GET, '/discount_codes/lookup', ['code' => $code]);
    }

    /**
     * Cria um codigo. Embrulha em `discount_code`.
     *
     * @param  array<string, mixed>  $discountCode  ex.: ['code' => 'SUMMERSALE10']
     */
    public function create(int|string $priceRuleId, array $discountCode): array
    {
        return $this->makeRequest(HttpMethod::POST, "/price_rules/{$priceRuleId}/discount_codes", [], ['discount_code' => $discountCode]);
    }

    /**
     * Atualiza um codigo. Embrulha em `discount_code`.
     *
     * @param  array<string, mixed>  $discountCode
     */
    public function update(int|string $priceRuleId, int|string $discountCodeId, array $discountCode): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/price_rules/{$priceRuleId}/discount_codes/{$discountCodeId}", [], ['discount_code' => $discountCode]);
    }

    /**
     * Remove um codigo.
     */
    public function delete(int|string $priceRuleId, int|string $discountCodeId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/price_rules/{$priceRuleId}/discount_codes/{$discountCodeId}");
    }

    /**
     * Cria codigos em lote (max 100 por chamada, assincrono). Embrulha em `discount_codes`.
     *
     * @param  array<int, array<string, mixed>>  $discountCodes  ex.: [['code' => 'A'], ['code' => 'B']]
     */
    public function createBatch(int|string $priceRuleId, array $discountCodes): array
    {
        return $this->makeRequest(HttpMethod::POST, "/price_rules/{$priceRuleId}/batch", [], ['discount_codes' => $discountCodes]);
    }

    /**
     * Status de um lote (discount_code_creation).
     */
    public function getBatch(int|string $priceRuleId, int|string $batchId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/price_rules/{$priceRuleId}/batch/{$batchId}");
    }

    /**
     * Codigos gerados por um lote (com erros por item, se houver).
     */
    public function listBatchCodes(int|string $priceRuleId, int|string $batchId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/price_rules/{$priceRuleId}/batch/{$batchId}/discount_codes");
    }
}
