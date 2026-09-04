<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Event;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Events (`events`) — log de acoes da loja (pedido criado, produto editado...).
 */
class EventMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista eventos da loja (1 pagina). Filtros: limit, since_id,
     * created_at_min/max, filter (ex.: Product,Order), verb, fields.
     *
     * @param  array<string, mixed>  $params
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/events', $params);
    }

    /**
     * Itera TODOS os eventos seguindo o cursor (page_info).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        yield from $this->eachPage('/events', 'events', $params, $limit);
    }

    /**
     * Conta eventos (created_at_min/max).
     *
     * @param  array<string, mixed>  $params
     */
    public function count(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/events/count', $params);
    }

    /**
     * Recupera um evento.
     */
    public function get(int|string $eventId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/events/{$eventId}", $params);
    }

    /**
     * Lista os eventos de um pedido.
     *
     * @param  array<string, mixed>  $params
     */
    public function listForOrder(int|string $orderId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/events", $params);
    }

    /**
     * Lista os eventos de um produto.
     *
     * @param  array<string, mixed>  $params
     */
    public function listForProduct(int|string $productId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/products/{$productId}/events", $params);
    }
}
