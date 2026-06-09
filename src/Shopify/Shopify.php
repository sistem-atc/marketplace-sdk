<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Billing\BillingMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Metafield\MetafieldMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Transaction\TransactionMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Webhooks;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;

class Shopify
{
    public function webhooks(MarketplaceIntegration $integration): Webhooks
    {
        return new Webhooks(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function billing(MarketplaceIntegration $integration): BillingMethods
    {
        return new BillingMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fulfillments(MarketplaceIntegration $integration): FulfillmentMethods
    {
        return new FulfillmentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function transactions(MarketplaceIntegration $integration): TransactionMethods
    {
        return new TransactionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function metafields(MarketplaceIntegration $integration): MetafieldMethods
    {
        return new MetafieldMethods(HttpClientFactory::make($integration), $integration);
    }
}
