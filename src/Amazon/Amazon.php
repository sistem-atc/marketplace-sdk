<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Reporting\ReportingMethods as AdsReportingMethods;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Amazon\Endpoints\Notifications;
use SistemAtc\Marketplaces\Amazon\Endpoints\Definitions;
use SistemAtc\Marketplaces\Amazon\Endpoints\Listings;
use SistemAtc\Marketplaces\Amazon\Endpoints\Messaging;
use SistemAtc\Marketplaces\Amazon\Endpoints\Reports;
use SistemAtc\Marketplaces\Amazon\Endpoints\Invoices;
use SistemAtc\Marketplaces\Amazon\Endpoints\Pricing;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentInboundV0;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentInbound;
use SistemAtc\Marketplaces\Amazon\Endpoints\FbaInboundEligibility;
use SistemAtc\Marketplaces\Amazon\Endpoints\FbaInventory;
use SistemAtc\Marketplaces\Amazon\Endpoints\ShippingV1;
use SistemAtc\Marketplaces\Amazon\Endpoints\Shipping;
use SistemAtc\Marketplaces\Amazon\Endpoints\MerchantFulfillment;
use SistemAtc\Marketplaces\Amazon\Endpoints\EasyShip;
use SistemAtc\Marketplaces\Amazon\Endpoints\Tracking;
use SistemAtc\Marketplaces\Amazon\Endpoints\SupplySources;
use SistemAtc\Marketplaces\Amazon\Endpoints\ExternalFulfillment;
use SistemAtc\Marketplaces\Amazon\Endpoints\ListingsRestrictions;
use SistemAtc\Marketplaces\Amazon\Endpoints\ProductFees;
use SistemAtc\Marketplaces\Amazon\Endpoints\CatalogItems;
use SistemAtc\Marketplaces\Amazon\Endpoints\CatalogItemsLegacy;
use SistemAtc\Marketplaces\Amazon\Endpoints\Promotions;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentOutbound;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentOutbound2026;
use SistemAtc\Marketplaces\Amazon\Endpoints\Awd;
use SistemAtc\Marketplaces\Amazon\Endpoints\Replenishment;
use SistemAtc\Marketplaces\Amazon\Endpoints\Uploads;
use SistemAtc\Marketplaces\Amazon\Endpoints\AplusContent;
use SistemAtc\Marketplaces\Amazon\Endpoints\OrdersV2026;
use SistemAtc\Marketplaces\Amazon\Endpoints\SellerWallet;
use SistemAtc\Marketplaces\Amazon\Endpoints\Feeds;
use SistemAtc\Marketplaces\Amazon\Endpoints\DataKiosk;
use SistemAtc\Marketplaces\Amazon\Endpoints\Sales;
use SistemAtc\Marketplaces\Amazon\Endpoints\Solicitations;
use SistemAtc\Marketplaces\Amazon\Endpoints\CustomerFeedback;
use SistemAtc\Marketplaces\Amazon\Endpoints\Services;
use SistemAtc\Marketplaces\Amazon\Endpoints\Vehicles;
use SistemAtc\Marketplaces\Amazon\Endpoints\ApplicationManagement;
use SistemAtc\Marketplaces\Amazon\Endpoints\ApplicationIntegrations;
use SistemAtc\Marketplaces\Amazon\Endpoints\DeliveryByAmazon;
use SistemAtc\Marketplaces\Amazon\Ads\Ads;

class Amazon
{
    /**
     * Hub completo da Amazon Ads API (SP/SB/SD/DSP/conta/audiencias...).
     * `ads()` continua devolvendo so o Reporting v3 por compatibilidade.
     */
    public function advertising(MarketplaceIntegration $integration, string $clientId): Ads
    {
        return new Ads($integration, $clientId);
    }

    public function client(MarketplaceIntegration $integration): Client
    {
        return new Client($integration);
    }

    /**
     * Amazon Ads API (advertising-api.amazon.com) — a integration aqui é a
     * de ADS (LwA próprio, escopo advertising::campaign_management), NÃO a
     * da SP-API; \$clientId é o client do Security Profile de ads, passado
     * pelo host (o SDK não lê config de consumidor).
     */
    public function ads(MarketplaceIntegration $integration, string $clientId): AdsReportingMethods
    {
        return new AdsReportingMethods($integration, $clientId);
    }

    public function orders(MarketplaceIntegration $integration): Orders
    {
        return $this->client($integration)->orders();
    }

    public function sellers(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\Sellers
    {
        return $this->client($integration)->sellers();
    }

    public function finances(MarketplaceIntegration $integration): Finances
    {
        return $this->client($integration)->finances();
    }

    public function notifications(MarketplaceIntegration $integration): Notifications
    {
        return $this->client($integration)->notifications();
    }

    public function listings(MarketplaceIntegration $integration): Listings
    {
        return $this->client($integration)->listings();
    }

    /** Product Type Definitions — o que define os atributos do cadastro. */
    public function definitions(MarketplaceIntegration $integration): Definitions
    {
        return $this->client($integration)->definitions();
    }

    public function messaging(MarketplaceIntegration $integration): Messaging
    {
        return $this->client($integration)->messaging();
    }

    public function reports(MarketplaceIntegration $integration): Reports
    {
        return $this->client($integration)->reports();
    }

    public function invoices(MarketplaceIntegration $integration): Invoices
    {
        return $this->client($integration)->invoices();
    }

    public function pricing(MarketplaceIntegration $integration): Pricing
    {
        return $this->client($integration)->pricing();
    }

    public function fulfillmentInboundV0(MarketplaceIntegration $integration): FulfillmentInboundV0
    {
        return $this->client($integration)->fulfillmentInboundV0();
    }

    public function fulfillmentInbound(MarketplaceIntegration $integration): FulfillmentInbound
    {
        return $this->client($integration)->fulfillmentInbound();
    }

    public function fbaInboundEligibility(MarketplaceIntegration $integration): FbaInboundEligibility
    {
        return $this->client($integration)->fbaInboundEligibility();
    }

    public function fbaInventory(MarketplaceIntegration $integration): FbaInventory
    {
        return $this->client($integration)->fbaInventory();
    }

    public function shippingV1(MarketplaceIntegration $integration): ShippingV1
    {
        return $this->client($integration)->shippingV1();
    }

    public function shipping(MarketplaceIntegration $integration): Shipping
    {
        return $this->client($integration)->shipping();
    }

    public function merchantFulfillment(MarketplaceIntegration $integration): MerchantFulfillment
    {
        return $this->client($integration)->merchantFulfillment();
    }

    public function easyShip(MarketplaceIntegration $integration): EasyShip
    {
        return $this->client($integration)->easyShip();
    }

    public function tracking(MarketplaceIntegration $integration): Tracking
    {
        return $this->client($integration)->tracking();
    }

    public function supplySources(MarketplaceIntegration $integration): SupplySources
    {
        return $this->client($integration)->supplySources();
    }

    public function externalFulfillment(MarketplaceIntegration $integration): ExternalFulfillment
    {
        return $this->client($integration)->externalFulfillment();
    }

    public function listingsRestrictions(MarketplaceIntegration $integration): ListingsRestrictions
    {
        return $this->client($integration)->listingsRestrictions();
    }

    public function productFees(MarketplaceIntegration $integration): ProductFees
    {
        return $this->client($integration)->productFees();
    }

    public function catalogItems(MarketplaceIntegration $integration): CatalogItems
    {
        return $this->client($integration)->catalogItems();
    }

    public function catalogItemsLegacy(MarketplaceIntegration $integration): CatalogItemsLegacy
    {
        return $this->client($integration)->catalogItemsLegacy();
    }

    public function promotions(MarketplaceIntegration $integration): Promotions
    {
        return $this->client($integration)->promotions();
    }

    public function fulfillmentOutbound(MarketplaceIntegration $integration): FulfillmentOutbound
    {
        return $this->client($integration)->fulfillmentOutbound();
    }

    public function fulfillmentOutbound2026(MarketplaceIntegration $integration): FulfillmentOutbound2026
    {
        return $this->client($integration)->fulfillmentOutbound2026();
    }

    public function awd(MarketplaceIntegration $integration): Awd
    {
        return $this->client($integration)->awd();
    }

    public function replenishment(MarketplaceIntegration $integration): Replenishment
    {
        return $this->client($integration)->replenishment();
    }

    public function uploads(MarketplaceIntegration $integration): Uploads
    {
        return $this->client($integration)->uploads();
    }

    public function aplusContent(MarketplaceIntegration $integration): AplusContent
    {
        return $this->client($integration)->aplusContent();
    }

    public function ordersV2026(MarketplaceIntegration $integration): OrdersV2026
    {
        return $this->client($integration)->ordersV2026();
    }

    public function sellerWallet(MarketplaceIntegration $integration): SellerWallet
    {
        return $this->client($integration)->sellerWallet();
    }

    public function feeds(MarketplaceIntegration $integration): Feeds
    {
        return $this->client($integration)->feeds();
    }

    public function dataKiosk(MarketplaceIntegration $integration): DataKiosk
    {
        return $this->client($integration)->dataKiosk();
    }

    public function sales(MarketplaceIntegration $integration): Sales
    {
        return $this->client($integration)->sales();
    }

    public function solicitations(MarketplaceIntegration $integration): Solicitations
    {
        return $this->client($integration)->solicitations();
    }

    public function customerFeedback(MarketplaceIntegration $integration): CustomerFeedback
    {
        return $this->client($integration)->customerFeedback();
    }

    // ---- Vendor Central (1P) ------------------------------------------

    public function vendorOrders(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorOrders
    {
        return $this->client($integration)->vendorOrders();
    }

    public function vendorShipments(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorShipments
    {
        return $this->client($integration)->vendorShipments();
    }

    public function vendorInvoices(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorInvoices
    {
        return $this->client($integration)->vendorInvoices();
    }

    public function vendorTransactionStatus(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorTransactionStatus
    {
        return $this->client($integration)->vendorTransactionStatus();
    }

    public function vendorDirectFulfillmentOrdersV1(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrdersV1
    {
        return $this->client($integration)->vendorDirectFulfillmentOrdersV1();
    }

    public function vendorDirectFulfillmentOrders(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrders
    {
        return $this->client($integration)->vendorDirectFulfillmentOrders();
    }

    public function vendorDirectFulfillmentShippingV1(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShippingV1
    {
        return $this->client($integration)->vendorDirectFulfillmentShippingV1();
    }

    public function vendorDirectFulfillmentShipping(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShipping
    {
        return $this->client($integration)->vendorDirectFulfillmentShipping();
    }

    public function vendorDirectFulfillmentTransactionsV1(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactionsV1
    {
        return $this->client($integration)->vendorDirectFulfillmentTransactionsV1();
    }

    public function vendorDirectFulfillmentTransactions(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactions
    {
        return $this->client($integration)->vendorDirectFulfillmentTransactions();
    }

    public function vendorDirectFulfillmentInventory(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentInventory
    {
        return $this->client($integration)->vendorDirectFulfillmentInventory();
    }

    public function vendorDirectFulfillmentPayments(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentPayments
    {
        return $this->client($integration)->vendorDirectFulfillmentPayments();
    }

    public function vendorDirectFulfillmentSandbox(MarketplaceIntegration $integration): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentSandbox
    {
        return $this->client($integration)->vendorDirectFulfillmentSandbox();
    }

    public function services(MarketplaceIntegration $integration): Services
    {
        return $this->client($integration)->services();
    }

    public function vehicles(MarketplaceIntegration $integration): Vehicles
    {
        return $this->client($integration)->vehicles();
    }

    public function applicationManagement(MarketplaceIntegration $integration): ApplicationManagement
    {
        return $this->client($integration)->applicationManagement();
    }

    public function applicationIntegrations(MarketplaceIntegration $integration): ApplicationIntegrations
    {
        return $this->client($integration)->applicationIntegrations();
    }

    public function deliveryByAmazon(MarketplaceIntegration $integration): DeliveryByAmazon
    {
        return $this->client($integration)->deliveryByAmazon();
    }
}
