<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa;

use SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog\BrandMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog\CategoryMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Info\InfoMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\ConectaLa\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;

/**
 * Conector da Conecta Lá (plataforma do "Shophub"). Ponto de entrada: cada
 * método devolve o grupo de endpoints já autenticado pra Integration.
 *
 *   $conecta = new ConectaLa();
 *   $conecta->infos($integration)->store();          // smoke test de credencial
 *   $conecta->orders($integration)->queue();         // fila de pedidos
 *   $conecta->orders($integration)->createNfe($nfe); // enviar nota
 */
class ConectaLa
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function infos(MarketplaceIntegration $integration): InfoMethods
    {
        return new InfoMethods(HttpClientFactory::make($integration), $integration);
    }

    public function brands(MarketplaceIntegration $integration): BrandMethods
    {
        return new BrandMethods(HttpClientFactory::make($integration), $integration);
    }

    public function categories(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }
}
