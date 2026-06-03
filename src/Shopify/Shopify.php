<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Webhooks;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;

class Shopify
{
    public function webhooks(MarketplaceIntegration $integration): Webhooks
    {
        return new Webhooks(HttpClientFactory::make($integration), $integration);
    }
}
