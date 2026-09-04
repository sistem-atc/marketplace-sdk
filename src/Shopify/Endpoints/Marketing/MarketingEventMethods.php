<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Marketing;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Marketing Events (`marketing_events`) — campanhas registradas por apps de
 * marketing, com engajamentos (impressoes, cliques, gasto) por dia.
 */
class MarketingEventMethods extends BaseMethods
{
    /**
     * Lista marketing events (limit, offset — este recurso pagina por offset).
     *
     * @param  array<string, mixed>  $params
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/marketing_events', $params);
    }

    /**
     * Conta marketing events.
     */
    public function count(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/marketing_events/count');
    }

    /**
     * Recupera um marketing event.
     */
    public function get(int|string $marketingEventId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/marketing_events/{$marketingEventId}");
    }

    /**
     * Cria um marketing event. Embrulha em `marketing_event`.
     *
     * @param  array<string, mixed>  $marketingEvent
     */
    public function create(array $marketingEvent): array
    {
        return $this->makeRequest(HttpMethod::POST, '/marketing_events', [], ['marketing_event' => $marketingEvent]);
    }

    /**
     * Atualiza um marketing event. Embrulha em `marketing_event`.
     *
     * @param  array<string, mixed>  $marketingEvent
     */
    public function update(int|string $marketingEventId, array $marketingEvent): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/marketing_events/{$marketingEventId}", [], ['marketing_event' => $marketingEvent]);
    }

    /**
     * Exclui um marketing event.
     */
    public function delete(int|string $marketingEventId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/marketing_events/{$marketingEventId}");
    }

    /**
     * Registra engajamentos (POST /marketing_events/{id}/engagements). Embrulha
     * em `engagements` — lista de ['occurred_on' => 'Y-m-d', 'impressions_count' => ..,
     * 'clicks_count' => .., 'ad_spend' => .., 'is_cumulative' => bool].
     *
     * @param  array<int, array<string, mixed>>  $engagements
     */
    public function engagements(int|string $marketingEventId, array $engagements): array
    {
        return $this->makeRequest(HttpMethod::POST, "/marketing_events/{$marketingEventId}/engagements", [], ['engagements' => array_values($engagements)]);
    }
}
