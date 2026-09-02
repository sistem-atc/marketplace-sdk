<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pricing;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Automatizações de preço (/pricing-automation/*) e referências de preço
 * (/suggestions/user/{id}/items).
 *
 * Regras hoje: `INT_EXT` (concorrência interna + externa) e `INT` (só
 * interna). Pré-condições da doc: item ativo, elegível, seller com reputação
 * amarela/verde; itens com automação ganham a tag `dynamic_standard_price`.
 */
class PricingAutomationMethods extends BaseMethods
{
    private const BASE = '/pricing-automation';

    /**
     * Regras de automação disponíveis pro item (GET /pricing-automation/items/{id}/rules):
     * `{item_id, rules[{rule_id}]}`. 412 quando o item não é automatizável.
     *
     * @return array<string, mixed>
     */
    public function itemRules(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'/items/'.rawurlencode($itemId).'/rules');
    }

    /**
     * Regras disponíveis pra um produto de catálogo
     * (GET /pricing-automation/products/{catalogProductId}/rules).
     *
     * @return array<string, mixed>
     */
    public function productRules(string $catalogProductId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'/products/'.rawurlencode($catalogProductId).'/rules');
    }

    /**
     * Itens do seller que têm automação (GET /pricing-automation/users/{id}/items):
     * `{items[], paging{total, offset, limit}}`. Limite máximo 100 — pagine
     * por offset até `paging.total`.
     *
     * @return array<string, mixed>
     */
    public function userItems(int|string $userId, int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE."/users/{$userId}/items", [
            'limit' => min($limit, 100),
            'offset' => $offset,
        ]);
    }

    /**
     * Automação vigente do item (GET /pricing-automation/items/{id}/automation):
     * `{item_id, status: ACTIVE|PAUSED, item_rule{rule_id}, min_price,
     * max_price, status_detail{cause, message}?}`. 404 automation_not_found
     * se nunca foi configurada.
     *
     * @return array<string, mixed>
     */
    public function getAutomation(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'/items/'.rawurlencode($itemId).'/automation');
    }

    /**
     * Cria automação (POST /pricing-automation/items/{id}/automation). Body
     * `{rule_id, min_price, max_price}` — min tem que ser menor que max e
     * ambos dentro da faixa aceita pela regra.
     *
     * @return array<string, mixed>
     */
    public function createAutomation(string $itemId, string $ruleId, float|int $minPrice, float|int $maxPrice): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/items/'.rawurlencode($itemId).'/automation',
            [],
            ['rule_id' => $ruleId, 'min_price' => $minPrice, 'max_price' => $maxPrice],
        );
    }

    /**
     * Cria automação apontando pro produto de catálogo em que o item compete
     * (POST /pricing-automation/items/{id}/automation/by-product/{catalogProductId}).
     * Mesmo body do createAutomation().
     *
     * @return array<string, mixed>
     */
    public function createAutomationByProduct(
        string $itemId,
        string $catalogProductId,
        string $ruleId,
        float|int $minPrice,
        float|int $maxPrice,
    ): array {
        return $this->makeRequest(
            HttpMethod::POST,
            self::BASE.'/items/'.rawurlencode($itemId).'/automation/by-product/'.rawurlencode($catalogProductId),
            [],
            ['rule_id' => $ruleId, 'min_price' => $minPrice, 'max_price' => $maxPrice],
        );
    }

    /**
     * Altera regra e/ou faixa (PUT /pricing-automation/items/{id}/automation).
     * Body completo `{rule_id, min_price, max_price}`.
     *
     * @return array<string, mixed>
     */
    public function updateAutomation(string $itemId, string $ruleId, float|int $minPrice, float|int $maxPrice): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            self::BASE.'/items/'.rawurlencode($itemId).'/automation',
            [],
            ['rule_id' => $ruleId, 'min_price' => $minPrice, 'max_price' => $maxPrice],
        );
    }

    /**
     * Remove a automação (DELETE /pricing-automation/items/{id}/automation).
     * 404 automation_not_found se não havia; 412 se o item não permite.
     *
     * @return array<string, mixed>
     */
    public function deleteAutomation(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, self::BASE.'/items/'.rawurlencode($itemId).'/automation');
    }

    /**
     * Histórico de mudanças de preço feitas pela automação
     * (GET /pricing-automation/items/{id}/price/history?days&page&size).
     * Defaults da doc: days=30, page=0, size=10. Resposta em
     * `result.content[{date_time, price, event, strategy_type, ...}]`.
     *
     * @return array<string, mixed>
     */
    public function priceHistory(string $itemId, ?int $days = null, ?int $page = null, ?int $size = null): array
    {
        $query = array_filter(['days' => $days, 'page' => $page, 'size' => $size], fn ($v) => $v !== null);

        return $this->makeRequest(
            HttpMethod::GET,
            self::BASE.'/items/'.rawurlencode($itemId).'/price/history',
            $query,
        );
    }

    /**
     * Itens do seller que têm referência de preço (GET /suggestions/user/{id}/items):
     * `{total, items[]}`. O detalhe de cada um sai em
     * ItemMethods::priceSuggestion() (/suggestions/items/{id}/details).
     *
     * @return array<string, mixed>
     */
    public function itemsWithPriceReference(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/suggestions/user/{$userId}/items");
    }
}
