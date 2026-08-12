<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Ads\Endpoints\Reporting\ReportingMethods as AdsReportingMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Authorization\AuthorizationMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\CustomerService\CustomerServiceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Finance\FinanceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Promotion\PromotionMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Reverse\ReverseMethods;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;

class Tiktok
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Authorization API — lojas autorizadas + shop_cipher (pos-OAuth). */
    public function authorization(MarketplaceIntegration $integration): AuthorizationMethods
    {
        return new AuthorizationMethods(HttpClientFactory::make($integration), $integration);
    }

    public function finance(MarketplaceIntegration $integration): FinanceMethods
    {
        return new FinanceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoice(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function category(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function promotion(MarketplaceIntegration $integration): PromotionMethods
    {
        return new PromotionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function reverse(MarketplaceIntegration $integration): ReverseMethods
    {
        return new ReverseMethods(HttpClientFactory::make($integration), $integration);
    }

    public function customerService(MarketplaceIntegration $integration): CustomerServiceMethods
    {
        return new CustomerServiceMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Marketing API (business-api.tiktok.com) — relatórios de gasto de ads
     * (GMV Max + auction). A integration aqui é a de ADS (token de longa
     * duração do portal TikTok for Business), NÃO a do Shop: outro host,
     * auth por header `Access-Token`, sem assinatura HMAC/shop_cipher.
     */
    public function ads(MarketplaceIntegration $integration): AdsReportingMethods
    {
        return new AdsReportingMethods($integration);
    }
}
