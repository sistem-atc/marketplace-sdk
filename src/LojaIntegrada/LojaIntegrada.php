<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Webhook\WebhookMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Support\HttpClientFactory;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Category\CategoryMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Brand\BrandMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Grade\GradeMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\ProductImage\ProductImageMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Stock\StockMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Price\PriceMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Customer\CustomerMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Coupon\CouponMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Shipping\ShippingMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Payment\PaymentMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\OrderStatus\OrderStatusMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\HtmlCode\HtmlCodeMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Seo\SeoMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Newsletter\NewsletterMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Enviali\EnvialiMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Marketing\MarketingMethods;

class LojaIntegrada
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function webhooks(MarketplaceIntegration $integration): WebhookMethods
    {
        return new WebhookMethods(HttpClientFactory::make($integration), $integration);
    }

    public function categories(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function brands(MarketplaceIntegration $integration): BrandMethods
    {
        return new BrandMethods(HttpClientFactory::make($integration), $integration);
    }

    public function grades(MarketplaceIntegration $integration): GradeMethods
    {
        return new GradeMethods(HttpClientFactory::make($integration), $integration);
    }

    public function productImages(MarketplaceIntegration $integration): ProductImageMethods
    {
        return new ProductImageMethods(HttpClientFactory::make($integration), $integration);
    }

    public function stock(MarketplaceIntegration $integration): StockMethods
    {
        return new StockMethods(HttpClientFactory::make($integration), $integration);
    }

    public function prices(MarketplaceIntegration $integration): PriceMethods
    {
        return new PriceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function customers(MarketplaceIntegration $integration): CustomerMethods
    {
        return new CustomerMethods(HttpClientFactory::make($integration), $integration);
    }

    public function coupons(MarketplaceIntegration $integration): CouponMethods
    {
        return new CouponMethods(HttpClientFactory::make($integration), $integration);
    }

    public function shipping(MarketplaceIntegration $integration): ShippingMethods
    {
        return new ShippingMethods(HttpClientFactory::make($integration), $integration);
    }

    public function payments(MarketplaceIntegration $integration): PaymentMethods
    {
        return new PaymentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function orderStatuses(MarketplaceIntegration $integration): OrderStatusMethods
    {
        return new OrderStatusMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoices(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function htmlCodes(MarketplaceIntegration $integration): HtmlCodeMethods
    {
        return new HtmlCodeMethods(HttpClientFactory::make($integration), $integration);
    }

    public function seo(MarketplaceIntegration $integration): SeoMethods
    {
        return new SeoMethods(HttpClientFactory::make($integration), $integration);
    }

    public function newsletter(MarketplaceIntegration $integration): NewsletterMethods
    {
        return new NewsletterMethods(HttpClientFactory::make($integration), $integration);
    }

    public function enviali(MarketplaceIntegration $integration): EnvialiMethods
    {
        return new EnvialiMethods(HttpClientFactory::make($integration), $integration);
    }

    public function marketing(MarketplaceIntegration $integration): MarketingMethods
    {
        return new MarketingMethods(HttpClientFactory::make($integration), $integration);
    }
}
