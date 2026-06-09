<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Finance\FinanceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Reverse\ReverseMethods;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;

class Tiktok
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function finance(MarketplaceIntegration $integration): FinanceMethods
    {
        return new FinanceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoice(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function reverse(MarketplaceIntegration $integration): ReverseMethods
    {
        return new ReverseMethods(HttpClientFactory::make($integration), $integration);
    }
}
