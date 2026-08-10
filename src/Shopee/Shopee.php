<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopee\Endpoints\Ads\AdsMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Payment\PaymentMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Webhook\WebhookMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Return\ReturnMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Chat\ChatMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Marketing\MarketingMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;

class Shopee
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function payment(MarketplaceIntegration $integration): PaymentMethods
    {
        return new PaymentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Catalogo de categorias e atributos (equivalente ao /categories do ML). */
    public function categories(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function webhooks(MarketplaceIntegration $integration): WebhookMethods
    {
        return new WebhookMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoices(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function returns(MarketplaceIntegration $integration): ReturnMethods
    {
        return new ReturnMethods(HttpClientFactory::make($integration), $integration);
    }

    public function chat(MarketplaceIntegration $integration): ChatMethods
    {
        return new ChatMethods(HttpClientFactory::make($integration), $integration);
    }

    public function marketing(MarketplaceIntegration $integration): MarketingMethods
    {
        return new MarketingMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Publicidade CPC (`/api/v2/ads`) — gasto diário da loja. Não confundir com
     * marketing(), que é desconto/voucher.
     */
    public function ads(MarketplaceIntegration $integration): AdsMethods
    {
        return new AdsMethods(HttpClientFactory::make($integration), $integration);
    }
}
