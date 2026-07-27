<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Magalu\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Delivery\DeliveryMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Webhooks\WebhookMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Product\PortfolioMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Claim\ClaimMethods;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;

class Magalu
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoices(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function deliveries(MarketplaceIntegration $integration): DeliveryMethods
    {
        return new DeliveryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function webhooks(MarketplaceIntegration $integration): WebhookMethods
    {
        return new WebhookMethods(HttpClientFactory::make($integration), $integration);
    }

    public function portfolio(MarketplaceIntegration $integration): PortfolioMethods
    {
        return new PortfolioMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Categorias e atributos (o que define os campos exigidos do SKU). */
    public function categories(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function claims(MarketplaceIntegration $integration): ClaimMethods
    {
        return new ClaimMethods(HttpClientFactory::make($integration), $integration);
    }
}
