<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Support\HttpClientFactory;

class LojaIntegrada
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }
}
