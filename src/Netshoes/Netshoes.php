<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Catalog\CatalogMethods;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Protocol\ProtocolMethods;
use SistemAtc\Marketplaces\Netshoes\Support\HttpClientFactory;

/**
 * Entry-point do conector Netshoes (Grupo Netshoes — Netshoes/Zattini, gateway
 * Sensedia). Auth = par de headers estaticos (client_id + access_token),
 * sem OAuth/exchange.
 *
 * Versao por recurso (Swagger oficial): pedidos/protocolos/catalogo em
 * /api/v1, produtos/precos/estoque em /api/v2 — cada metodo informa o path
 * completo (ver docblock de OrderMethods sobre a divergencia v1 x v2 nos
 * pedidos).
 *
 * Uso: MarketPlaces::Netshoes()->orders($integration)->getOrder($orderNumber);
 */
class Netshoes
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function protocols(MarketplaceIntegration $integration): ProtocolMethods
    {
        return new ProtocolMethods(HttpClientFactory::make($integration), $integration);
    }

    public function catalog(MarketplaceIntegration $integration): CatalogMethods
    {
        return new CatalogMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }
}
