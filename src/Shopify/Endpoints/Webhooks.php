<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class Webhooks extends BaseMethods
{
    public function list(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, 'webhooks');
        return $response['webhooks'] ?? [];
    }

    public function create(string $topic, string $address, string $format = 'json'): array
    {
        return $this->makeRequest(HttpMethod::POST, 'webhooks', [], [
            'webhook' => [
                'topic' => $topic,
                'address' => $address,
                'format' => $format,
            ],
        ]);
    }

    public function delete(int|string $webhookId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "webhooks/{$webhookId}");
    }

    /**
     * Conta webhooks registrados (filtros: topic, address).
     *
     * @param  array<string, mixed>  $params
     */
    public function count(array $params = []): int
    {
        $response = $this->makeRequest(HttpMethod::GET, 'webhooks/count', $params);

        return $response['count'] ?? 0;
    }

    /**
     * Recupera um webhook pelo id.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function get(int|string $webhookId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "webhooks/{$webhookId}", $params);
    }

    /**
     * Atualiza um webhook (address, topic, format, fields, metafield_namespaces...).
     *
     * @param  array<string, mixed>  $webhook
     */
    public function update(int|string $webhookId, array $webhook): array
    {
        return $this->makeRequest(HttpMethod::PUT, "webhooks/{$webhookId}", [], ['webhook' => $webhook]);
    }
}
