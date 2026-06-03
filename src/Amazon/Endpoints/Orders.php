<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

class Orders
{
    public function __construct(
        protected PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {}

    public function getOrder(string $amazonOrderId, int $retryAttempt = 0): array
    {
        $response = $this->httpClient->get("/orders/v0/orders/{$amazonOrderId}");

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            Log::warning("Amazon SP-API 429/5xx, retrying in {$sleep}s", ['order' => $amazonOrderId]);
            sleep($sleep);
            return $this->getOrder($amazonOrderId, $retryAttempt + 1);
        }

        return $response->json() ?? [];
    }
    
    public function getOrderItems(string $amazonOrderId, int $retryAttempt = 0): array
    {
        $response = $this->httpClient->get("/orders/v0/orders/{$amazonOrderId}/orderItems");
        
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->getOrderItems($amazonOrderId, $retryAttempt + 1);
        }

        return $response->json() ?? [];
    }
}
