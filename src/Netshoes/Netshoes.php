<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Netshoes\Support\HttpClientFactory;

/**
 * Entry-point do conector Netshoes (Grupo Netshoes — Netshoes/Zattini, gateway
 * Sensedia). Auth = par de headers estaticos (client_id + access_token),
 * sem OAuth/exchange. API V2.
 *
 * Uso: MarketPlaces::Netshoes()->orders($integration)->getOrder($orderNumber);
 */
class Netshoes
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }
}
