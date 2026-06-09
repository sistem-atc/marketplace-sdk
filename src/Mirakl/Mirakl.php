<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Mirakl\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Mirakl\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Mirakl\Endpoints\Finance\FinanceMethods;
use SistemAtc\Marketplaces\Mirakl\Support\HttpClientFactory;

class Mirakl
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function finance(MarketplaceIntegration $integration): FinanceMethods
    {
        return new FinanceMethods(HttpClientFactory::make($integration), $integration);
    }
}
