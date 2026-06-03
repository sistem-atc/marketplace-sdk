<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Finance\FinanceMethods;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;

class Tiktok
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoices(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function finance(MarketplaceIntegration $integration): FinanceMethods
    {
        return new FinanceMethods(HttpClientFactory::make($integration), $integration);
    }
}
