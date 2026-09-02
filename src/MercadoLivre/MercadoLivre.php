<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\User\UserMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipment\ShipmentMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Billing\BillingMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Inventory\InventoryMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Item\ItemMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Question\QuestionMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Message\MessageMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Claim\ClaimMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Category\CategoryMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Promotion\PromotionMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Advertising\AdvertisingMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pack\PackMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Flex\FlexMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pickup\PickupMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Bundle\BundleMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipping\ShippingMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Catalog\CatalogMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\UserProduct\UserProductMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Moderation\ModerationMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Metrics\MetricsMethods;

class MercadoLivre
{
    public function user(MarketplaceIntegration $integration): UserMethods
    {
        return new UserMethods(HttpClientFactory::make($integration), $integration);
    }

    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoices(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function shipments(MarketplaceIntegration $integration): ShipmentMethods
    {
        return new ShipmentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function billing(MarketplaceIntegration $integration): BillingMethods
    {
        return new BillingMethods(HttpClientFactory::make($integration), $integration);
    }

    public function items(MarketplaceIntegration $integration): ItemMethods
    {
        return new ItemMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Inventory / Fulfillment — estoque dos itens no Full.
     */
    public function inventory(MarketplaceIntegration $integration): InventoryMethods
    {
        return new InventoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function questions(MarketplaceIntegration $integration): QuestionMethods
    {
        return new QuestionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function messages(MarketplaceIntegration $integration): MessageMethods
    {
        return new MessageMethods(HttpClientFactory::make($integration), $integration);
    }

    public function claims(MarketplaceIntegration $integration): ClaimMethods
    {
        return new ClaimMethods(HttpClientFactory::make($integration), $integration);
    }

    public function categories(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Seller Promotions — campanhas e promoções (DEAL, LIGHTNING, PRE_NEGOTIATED, etc).
     * Usa app_version=v2 (obrigatório).
     */
    public function promotions(MarketplaceIntegration $integration): PromotionMethods
    {
        return new PromotionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function advertising(MarketplaceIntegration $integration): AdvertisingMethods
    {
        return new AdvertisingMethods(HttpClientFactory::make($integration), $integration);
    }

    public function packs(MarketplaceIntegration $integration): PackMethods
    {
        return new PackMethods(HttpClientFactory::make($integration), $integration);
    }

    public function flex(MarketplaceIntegration $integration): FlexMethods
    {
        return new FlexMethods(HttpClientFactory::make($integration), $integration);
    }

    public function pickup(MarketplaceIntegration $integration): PickupMethods
    {
        return new PickupMethods(HttpClientFactory::make($integration), $integration);
    }

    public function bundles(MarketplaceIntegration $integration): BundleMethods
    {
        return new BundleMethods(HttpClientFactory::make($integration), $integration);
    }

    public function shipping(MarketplaceIntegration $integration): ShippingMethods
    {
        return new ShippingMethods(HttpClientFactory::make($integration), $integration);
    }

    public function catalog(MarketplaceIntegration $integration): CatalogMethods
    {
        return new CatalogMethods(HttpClientFactory::make($integration), $integration);
    }

    public function userProducts(MarketplaceIntegration $integration): UserProductMethods
    {
        return new UserProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function moderations(MarketplaceIntegration $integration): ModerationMethods
    {
        return new ModerationMethods(HttpClientFactory::make($integration), $integration);
    }

    public function metrics(MarketplaceIntegration $integration): MetricsMethods
    {
        return new MetricsMethods(HttpClientFactory::make($integration), $integration);
    }
}
