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
}
