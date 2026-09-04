<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\GiftCard;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Gift Cards (`gift_cards`) + ajustes de saldo (`gift_cards/{id}/adjustments`).
 * Exige plano Shopify Plus / permissao de gift cards.
 */
class GiftCardMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista gift cards (1 pagina). Filtros: status (enabled|disabled), limit, since_id, fields.
     *
     * @param  array<string, mixed>  $params
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/gift_cards', $params);
    }

    /**
     * Itera TODOS os gift cards seguindo o cursor (page_info).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        yield from $this->eachPage('/gift_cards', 'gift_cards', $params, $limit);
    }

    /**
     * Conta gift cards (status).
     *
     * @param  array<string, mixed>  $params
     */
    public function count(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/gift_cards/count', $params);
    }

    /**
     * Recupera um gift card.
     */
    public function get(int|string $giftCardId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/gift_cards/{$giftCardId}");
    }

    /**
     * Busca gift cards (GET /gift_cards/search?query=...). Extras: order, limit,
     * fields, created_at_min/max, updated_at_min/max.
     *
     * @param  array<string, mixed>  $params
     */
    public function search(string $query, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/gift_cards/search', array_merge(['query' => $query], $params));
    }

    /**
     * Cria um gift card. Embrulha em `gift_card`.
     *
     * @param  array<string, mixed>  $giftCard
     */
    public function create(array $giftCard): array
    {
        return $this->makeRequest(HttpMethod::POST, '/gift_cards', [], ['gift_card' => $giftCard]);
    }

    /**
     * Atualiza um gift card (note, expires_on, template_suffix...). Embrulha em `gift_card`.
     *
     * @param  array<string, mixed>  $giftCard
     */
    public function update(int|string $giftCardId, array $giftCard): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/gift_cards/{$giftCardId}", [], ['gift_card' => $giftCard]);
    }

    /**
     * Desabilita um gift card (irreversivel). Embrulha em `gift_card`.
     */
    public function disable(int|string $giftCardId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/gift_cards/{$giftCardId}/disable", [], ['gift_card' => ['id' => $giftCardId]]);
    }

    /**
     * Lista os ajustes de saldo de um gift card.
     */
    public function listAdjustments(int|string $giftCardId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/gift_cards/{$giftCardId}/adjustments");
    }

    /**
     * Recupera um ajuste de saldo.
     */
    public function getAdjustment(int|string $giftCardId, int|string $adjustmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/gift_cards/{$giftCardId}/adjustments/{$adjustmentId}");
    }

    /**
     * Cria um ajuste de saldo. Embrulha em `adjustment` — ex.: ['amount' => 10.0, 'note' => '...'].
     *
     * @param  array<string, mixed>  $adjustment
     */
    public function createAdjustment(int|string $giftCardId, array $adjustment): array
    {
        return $this->makeRequest(HttpMethod::POST, "/gift_cards/{$giftCardId}/adjustments", [], ['adjustment' => $adjustment]);
    }
}
