<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Webhook\WebhookMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Support\HttpClientFactory;

class LojaIntegrada
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function webhooks(MarketplaceIntegration $integration): WebhookMethods
    {
        return new WebhookMethods(HttpClientFactory::make($integration), $integration);
    }
}
