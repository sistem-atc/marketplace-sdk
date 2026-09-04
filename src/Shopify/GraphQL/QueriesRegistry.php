<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL;

use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\AppQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\BulkOperationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CartQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CatalogQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CheckoutQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CollectionQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CompanyQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ContentQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CustomerQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\DeliveryQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\DiscountQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\DraftOrderQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\EventQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\FileQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\FulfillmentQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\GiftCardQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\InventoryQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\LocationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MarketQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MarketingQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MetafieldQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MetaobjectQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MiscQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\OrderQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\PaymentQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\PointOfSaleQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\PriceQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ProductQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\PublicationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\RefundQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ReturnQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\SegmentQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\SellingPlanQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ShopQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\StaffQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\StoreCreditQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\SubscriptionQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\TranslationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\UrlRedirectQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ValidationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\WebQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\WebhookQueries;

/**
 * Acesso por dominio as queries GraphQL (arquivo gerado).
 */
final class QueriesRegistry
{
    public function __construct(private GraphQLClient $client) {}

    public function app(): AppQueries
    {
        return new AppQueries($this->client);
    }

    public function bulkOperations(): BulkOperationQueries
    {
        return new BulkOperationQueries($this->client);
    }

    public function cart(): CartQueries
    {
        return new CartQueries($this->client);
    }

    public function catalogs(): CatalogQueries
    {
        return new CatalogQueries($this->client);
    }

    public function checkouts(): CheckoutQueries
    {
        return new CheckoutQueries($this->client);
    }

    public function collections(): CollectionQueries
    {
        return new CollectionQueries($this->client);
    }

    public function companies(): CompanyQueries
    {
        return new CompanyQueries($this->client);
    }

    public function content(): ContentQueries
    {
        return new ContentQueries($this->client);
    }

    public function customers(): CustomerQueries
    {
        return new CustomerQueries($this->client);
    }

    public function delivery(): DeliveryQueries
    {
        return new DeliveryQueries($this->client);
    }

    public function discounts(): DiscountQueries
    {
        return new DiscountQueries($this->client);
    }

    public function draftOrders(): DraftOrderQueries
    {
        return new DraftOrderQueries($this->client);
    }

    public function events(): EventQueries
    {
        return new EventQueries($this->client);
    }

    public function files(): FileQueries
    {
        return new FileQueries($this->client);
    }

    public function fulfillments(): FulfillmentQueries
    {
        return new FulfillmentQueries($this->client);
    }

    public function giftCards(): GiftCardQueries
    {
        return new GiftCardQueries($this->client);
    }

    public function inventory(): InventoryQueries
    {
        return new InventoryQueries($this->client);
    }

    public function locations(): LocationQueries
    {
        return new LocationQueries($this->client);
    }

    public function markets(): MarketQueries
    {
        return new MarketQueries($this->client);
    }

    public function marketing(): MarketingQueries
    {
        return new MarketingQueries($this->client);
    }

    public function metafields(): MetafieldQueries
    {
        return new MetafieldQueries($this->client);
    }

    public function metaobjects(): MetaobjectQueries
    {
        return new MetaobjectQueries($this->client);
    }

    public function misc(): MiscQueries
    {
        return new MiscQueries($this->client);
    }

    public function orders(): OrderQueries
    {
        return new OrderQueries($this->client);
    }

    public function payments(): PaymentQueries
    {
        return new PaymentQueries($this->client);
    }

    public function pointOfSale(): PointOfSaleQueries
    {
        return new PointOfSaleQueries($this->client);
    }

    public function prices(): PriceQueries
    {
        return new PriceQueries($this->client);
    }

    public function products(): ProductQueries
    {
        return new ProductQueries($this->client);
    }

    public function publications(): PublicationQueries
    {
        return new PublicationQueries($this->client);
    }

    public function refunds(): RefundQueries
    {
        return new RefundQueries($this->client);
    }

    public function returns(): ReturnQueries
    {
        return new ReturnQueries($this->client);
    }

    public function segments(): SegmentQueries
    {
        return new SegmentQueries($this->client);
    }

    public function sellingPlans(): SellingPlanQueries
    {
        return new SellingPlanQueries($this->client);
    }

    public function shop(): ShopQueries
    {
        return new ShopQueries($this->client);
    }

    public function staff(): StaffQueries
    {
        return new StaffQueries($this->client);
    }

    public function storeCredit(): StoreCreditQueries
    {
        return new StoreCreditQueries($this->client);
    }

    public function subscriptions(): SubscriptionQueries
    {
        return new SubscriptionQueries($this->client);
    }

    public function translations(): TranslationQueries
    {
        return new TranslationQueries($this->client);
    }

    public function urlRedirects(): UrlRedirectQueries
    {
        return new UrlRedirectQueries($this->client);
    }

    public function validations(): ValidationQueries
    {
        return new ValidationQueries($this->client);
    }

    public function web(): WebQueries
    {
        return new WebQueries($this->client);
    }

    public function webhooks(): WebhookQueries
    {
        return new WebhookQueries($this->client);
    }
}
