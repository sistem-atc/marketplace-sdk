<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL;

use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\AppMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\BulkOperationMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CartMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CatalogMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CheckoutMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CollectionMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CompanyMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ContentMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CustomerMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\DeliveryMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\DiscountMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\DraftOrderMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\EventMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\FileMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\FulfillmentMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\GiftCardMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\InventoryMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\LocationMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MarketMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MarketingMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MetafieldMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MetaobjectMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MiscMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\OrderMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\PaymentMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\PointOfSaleMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\PriceMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ProductMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\PublicationMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\RefundMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ReturnMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\SegmentMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\SellingPlanMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ShopMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\StoreCreditMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\SubscriptionMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\TranslationMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\UrlRedirectMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ValidationMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\WebMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\WebhookMutations;

/**
 * Acesso por dominio as mutations GraphQL (arquivo gerado).
 */
final class MutationsRegistry
{
    public function __construct(private GraphQLClient $client) {}

    public function app(): AppMutations
    {
        return new AppMutations($this->client);
    }

    public function bulkOperations(): BulkOperationMutations
    {
        return new BulkOperationMutations($this->client);
    }

    public function cart(): CartMutations
    {
        return new CartMutations($this->client);
    }

    public function catalogs(): CatalogMutations
    {
        return new CatalogMutations($this->client);
    }

    public function checkouts(): CheckoutMutations
    {
        return new CheckoutMutations($this->client);
    }

    public function collections(): CollectionMutations
    {
        return new CollectionMutations($this->client);
    }

    public function companies(): CompanyMutations
    {
        return new CompanyMutations($this->client);
    }

    public function content(): ContentMutations
    {
        return new ContentMutations($this->client);
    }

    public function customers(): CustomerMutations
    {
        return new CustomerMutations($this->client);
    }

    public function delivery(): DeliveryMutations
    {
        return new DeliveryMutations($this->client);
    }

    public function discounts(): DiscountMutations
    {
        return new DiscountMutations($this->client);
    }

    public function draftOrders(): DraftOrderMutations
    {
        return new DraftOrderMutations($this->client);
    }

    public function events(): EventMutations
    {
        return new EventMutations($this->client);
    }

    public function files(): FileMutations
    {
        return new FileMutations($this->client);
    }

    public function fulfillments(): FulfillmentMutations
    {
        return new FulfillmentMutations($this->client);
    }

    public function giftCards(): GiftCardMutations
    {
        return new GiftCardMutations($this->client);
    }

    public function inventory(): InventoryMutations
    {
        return new InventoryMutations($this->client);
    }

    public function locations(): LocationMutations
    {
        return new LocationMutations($this->client);
    }

    public function markets(): MarketMutations
    {
        return new MarketMutations($this->client);
    }

    public function marketing(): MarketingMutations
    {
        return new MarketingMutations($this->client);
    }

    public function metafields(): MetafieldMutations
    {
        return new MetafieldMutations($this->client);
    }

    public function metaobjects(): MetaobjectMutations
    {
        return new MetaobjectMutations($this->client);
    }

    public function misc(): MiscMutations
    {
        return new MiscMutations($this->client);
    }

    public function orders(): OrderMutations
    {
        return new OrderMutations($this->client);
    }

    public function payments(): PaymentMutations
    {
        return new PaymentMutations($this->client);
    }

    public function pointOfSale(): PointOfSaleMutations
    {
        return new PointOfSaleMutations($this->client);
    }

    public function prices(): PriceMutations
    {
        return new PriceMutations($this->client);
    }

    public function products(): ProductMutations
    {
        return new ProductMutations($this->client);
    }

    public function publications(): PublicationMutations
    {
        return new PublicationMutations($this->client);
    }

    public function refunds(): RefundMutations
    {
        return new RefundMutations($this->client);
    }

    public function returns(): ReturnMutations
    {
        return new ReturnMutations($this->client);
    }

    public function segments(): SegmentMutations
    {
        return new SegmentMutations($this->client);
    }

    public function sellingPlans(): SellingPlanMutations
    {
        return new SellingPlanMutations($this->client);
    }

    public function shop(): ShopMutations
    {
        return new ShopMutations($this->client);
    }

    public function storeCredit(): StoreCreditMutations
    {
        return new StoreCreditMutations($this->client);
    }

    public function subscriptions(): SubscriptionMutations
    {
        return new SubscriptionMutations($this->client);
    }

    public function translations(): TranslationMutations
    {
        return new TranslationMutations($this->client);
    }

    public function urlRedirects(): UrlRedirectMutations
    {
        return new UrlRedirectMutations($this->client);
    }

    public function validations(): ValidationMutations
    {
        return new ValidationMutations($this->client);
    }

    public function web(): WebMutations
    {
        return new WebMutations($this->client);
    }

    public function webhooks(): WebhookMutations
    {
        return new WebhookMutations($this->client);
    }
}
