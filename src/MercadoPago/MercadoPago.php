<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Payments\PaymentsMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Settlement\SettlementMethods;
use SistemAtc\Marketplaces\MercadoPago\Support\HttpClientFactory;

class MercadoPago
{
    public function payments(MarketplaceIntegration $integration): PaymentsMethods
    {
        return new PaymentsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function settlement(MarketplaceIntegration $integration): SettlementMethods
    {
        return new SettlementMethods(HttpClientFactory::make($integration), $integration);
    }
}
