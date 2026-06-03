<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopee\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Payment\PaymentMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;

class Shopee
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function payment(MarketplaceIntegration $integration): PaymentMethods
    {
        return new PaymentMethods(HttpClientFactory::make($integration), $integration);
    }
}
