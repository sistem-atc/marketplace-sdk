<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;

class Webhooks
{
    public function __construct(
        protected PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {}

    public function list(): array
    {
        $response = $this->httpClient->get('webhooks.json');
        return $response->json()['webhooks'] ?? [];
    }

    public function create(string $topic, string $address, string $format = 'json'): array
    {
        $response = $this->httpClient->post('webhooks.json', [
            'webhook' => [
                'topic' => $topic,
                'address' => $address,
                'format' => $format,
            ],
        ]);
        return $response->json()['webhook'] ?? [];
    }

    public function delete(int|string $webhookId): array
    {
        $response = $this->httpClient->delete("webhooks/{$webhookId}.json");
        return $response->json() ?? [];
    }
}
