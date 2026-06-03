<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\User\UserMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipment\ShipmentMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Billing\BillingMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Item\ItemMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Question\QuestionMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Message\MessageMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Claim\ClaimMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Category\CategoryMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;

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
}
