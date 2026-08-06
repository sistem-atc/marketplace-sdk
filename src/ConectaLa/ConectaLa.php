<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa;

use SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog\BrandMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog\CatalogMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog\CategoryMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog\CollectionMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Financial\FinancialMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Info\InfoMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Product\VariantMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Logistics\TrackingMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Account\CompanyMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Account\StoreMethods;
use SistemAtc\Marketplaces\ConectaLa\Endpoints\Account\UserMethods;
use SistemAtc\Marketplaces\ConectaLa\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;

/**
 * ⚠️ NO BUNKER ESTA INTEGRAÇÃO CHAMA-SE "SHOPHUB". "Conecta Lá" é o nome da
 * PLATAFORMA/API (domínio conectala.com.br) que o Shophub usa; o conector ficou
 * com o nome técnico da API, mas a Integration no Bunker entra como `shophub`.
 * (Registrado aqui pra não depender de memória — Shophub == ConectaLa.)
 *
 * Conector da Conecta Lá. Auth por HEADER (x-api-key/x-store-key/...). Sincronismo
 * é por FILA/POLLING (não há webhook): `orders()->queue()` + baixa via remove;
 * `products()->modifiedQueue()` + baixa. Cotação de frete é callback INBOUND (a
 * plataforma chama a URL do seller) — endpoint que o Bunker expõe, não daqui.
 *
 * Ponto de entrada: cada método devolve o grupo de endpoints já autenticado.
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

    public function catalogs(MarketplaceIntegration $integration): CatalogMethods
    {
        return new CatalogMethods(HttpClientFactory::make($integration), $integration);
    }

    public function collections(MarketplaceIntegration $integration): CollectionMethods
    {
        return new CollectionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function financial(MarketplaceIntegration $integration): FinancialMethods
    {
        return new FinancialMethods(HttpClientFactory::make($integration), $integration);
    }

    public function variations(MarketplaceIntegration $integration): VariantMethods
    {
        return new VariantMethods(HttpClientFactory::make($integration), $integration);
    }

    public function tracking(MarketplaceIntegration $integration): TrackingMethods
    {
        return new TrackingMethods(HttpClientFactory::make($integration), $integration);
    }

    public function companies(MarketplaceIntegration $integration): CompanyMethods
    {
        return new CompanyMethods(HttpClientFactory::make($integration), $integration);
    }

    public function stores(MarketplaceIntegration $integration): StoreMethods
    {
        return new StoreMethods(HttpClientFactory::make($integration), $integration);
    }

    public function users(MarketplaceIntegration $integration): UserMethods
    {
        return new UserMethods(HttpClientFactory::make($integration), $integration);
    }
}
