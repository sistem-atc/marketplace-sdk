<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;

class Finances
{
    public function __construct(
        protected PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {}

    public function listFinancialEventsByOrderId(string $amazonOrderId): array
    {
        $response = $this->httpClient->get("/finances/v0/orders/{$amazonOrderId}/financialEvents");
        return $response->json() ?? [];
    }
}
