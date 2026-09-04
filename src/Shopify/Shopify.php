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
use SistemAtc\Marketplaces\Shopify\Endpoints\Analytics\AnalyticsMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Shopify\Endpoints\Checkout\CheckoutMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Store\StoreSettingsMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\ApplePayCertificateMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\ShopifyPaymentsMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Billing\ApplicationChargeMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\BlogMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\AssetMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentOrderRequestMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Shipping\CarrierServiceMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Collection\CollectionMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Customer\CustomerMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Discount\DiscountCodeMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Order\OrderRiskMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentOrderMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentServiceMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\DraftOrder\DraftOrderMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Event\EventMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\GiftCard\GiftCardMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductImageMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Inventory\InventoryMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Marketing\MarketingEventMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\MobileApp\MobilePlatformApplicationMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\PageMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\DisputeEvidenceMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PaymentMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PaymentGatewayMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\VariantMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\SmartCollectionMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductListingMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ResourceFeedbackMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Discount\PriceRuleMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PayoutMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Refund\RefundMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Shop\ShopMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\ThemeMethods;
use SistemAtc\Marketplaces\Shopify\GraphQL\Admin;

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

    public function analytics(MarketplaceIntegration $integration): AnalyticsMethods
    {
        return new AnalyticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function checkouts(MarketplaceIntegration $integration): CheckoutMethods
    {
        return new CheckoutMethods(HttpClientFactory::make($integration), $integration);
    }

    public function storeSettings(MarketplaceIntegration $integration): StoreSettingsMethods
    {
        return new StoreSettingsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function applePayCertificates(MarketplaceIntegration $integration): ApplePayCertificateMethods
    {
        return new ApplePayCertificateMethods(HttpClientFactory::make($integration), $integration);
    }

    public function shopifyPayments(MarketplaceIntegration $integration): ShopifyPaymentsMethods
    {
        return new ShopifyPaymentsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function applicationCharges(MarketplaceIntegration $integration): ApplicationChargeMethods
    {
        return new ApplicationChargeMethods(HttpClientFactory::make($integration), $integration);
    }

    public function blogs(MarketplaceIntegration $integration): BlogMethods
    {
        return new BlogMethods(HttpClientFactory::make($integration), $integration);
    }

    public function assets(MarketplaceIntegration $integration): AssetMethods
    {
        return new AssetMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fulfillmentOrderRequests(MarketplaceIntegration $integration): FulfillmentOrderRequestMethods
    {
        return new FulfillmentOrderRequestMethods(HttpClientFactory::make($integration), $integration);
    }

    public function carrierServices(MarketplaceIntegration $integration): CarrierServiceMethods
    {
        return new CarrierServiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function collections(MarketplaceIntegration $integration): CollectionMethods
    {
        return new CollectionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function customers(MarketplaceIntegration $integration): CustomerMethods
    {
        return new CustomerMethods(HttpClientFactory::make($integration), $integration);
    }

    public function discountCodes(MarketplaceIntegration $integration): DiscountCodeMethods
    {
        return new DiscountCodeMethods(HttpClientFactory::make($integration), $integration);
    }

    public function orderRisks(MarketplaceIntegration $integration): OrderRiskMethods
    {
        return new OrderRiskMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fulfillmentOrders(MarketplaceIntegration $integration): FulfillmentOrderMethods
    {
        return new FulfillmentOrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fulfillmentServices(MarketplaceIntegration $integration): FulfillmentServiceMethods
    {
        return new FulfillmentServiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function draftOrders(MarketplaceIntegration $integration): DraftOrderMethods
    {
        return new DraftOrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function events(MarketplaceIntegration $integration): EventMethods
    {
        return new EventMethods(HttpClientFactory::make($integration), $integration);
    }

    public function giftCards(MarketplaceIntegration $integration): GiftCardMethods
    {
        return new GiftCardMethods(HttpClientFactory::make($integration), $integration);
    }

    public function productImages(MarketplaceIntegration $integration): ProductImageMethods
    {
        return new ProductImageMethods(HttpClientFactory::make($integration), $integration);
    }

    public function inventory(MarketplaceIntegration $integration): InventoryMethods
    {
        return new InventoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function marketingEvents(MarketplaceIntegration $integration): MarketingEventMethods
    {
        return new MarketingEventMethods(HttpClientFactory::make($integration), $integration);
    }

    public function mobilePlatformApplications(MarketplaceIntegration $integration): MobilePlatformApplicationMethods
    {
        return new MobilePlatformApplicationMethods(HttpClientFactory::make($integration), $integration);
    }

    public function pages(MarketplaceIntegration $integration): PageMethods
    {
        return new PageMethods(HttpClientFactory::make($integration), $integration);
    }

    public function disputeEvidences(MarketplaceIntegration $integration): DisputeEvidenceMethods
    {
        return new DisputeEvidenceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function checkoutPayments(MarketplaceIntegration $integration): PaymentMethods
    {
        return new PaymentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function paymentGateways(MarketplaceIntegration $integration): PaymentGatewayMethods
    {
        return new PaymentGatewayMethods(HttpClientFactory::make($integration), $integration);
    }

    public function variants(MarketplaceIntegration $integration): VariantMethods
    {
        return new VariantMethods(HttpClientFactory::make($integration), $integration);
    }

    public function smartCollections(MarketplaceIntegration $integration): SmartCollectionMethods
    {
        return new SmartCollectionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function productListings(MarketplaceIntegration $integration): ProductListingMethods
    {
        return new ProductListingMethods(HttpClientFactory::make($integration), $integration);
    }

    public function resourceFeedback(MarketplaceIntegration $integration): ResourceFeedbackMethods
    {
        return new ResourceFeedbackMethods(HttpClientFactory::make($integration), $integration);
    }

    public function priceRules(MarketplaceIntegration $integration): PriceRuleMethods
    {
        return new PriceRuleMethods(HttpClientFactory::make($integration), $integration);
    }

    public function payouts(MarketplaceIntegration $integration): PayoutMethods
    {
        return new PayoutMethods(HttpClientFactory::make($integration), $integration);
    }

    public function refunds(MarketplaceIntegration $integration): RefundMethods
    {
        return new RefundMethods(HttpClientFactory::make($integration), $integration);
    }

    public function shop(MarketplaceIntegration $integration): ShopMethods
    {
        return new ShopMethods(HttpClientFactory::make($integration), $integration);
    }

    public function themes(MarketplaceIntegration $integration): ThemeMethods
    {
        return new ThemeMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Admin API GraphQL (schema 2026-07; graphql_api_version >= 2025-10). */
    public function graphql(MarketplaceIntegration $integration): Admin
    {
        return Admin::forIntegration($integration);
    }
}
