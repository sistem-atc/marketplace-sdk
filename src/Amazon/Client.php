<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Amazon\Endpoints\Notifications;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Amazon\Endpoints\Definitions;
use SistemAtc\Marketplaces\Amazon\Endpoints\Listings;
use SistemAtc\Marketplaces\Amazon\Endpoints\Messaging;
use SistemAtc\Marketplaces\Amazon\Endpoints\Reports;
use SistemAtc\Marketplaces\Amazon\Endpoints\Invoices;
use SistemAtc\Marketplaces\Amazon\Endpoints\Tokens;
use SistemAtc\Marketplaces\Amazon\Endpoints\Pricing;
use SistemAtc\Marketplaces\Amazon\Support\TokenRefresher;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
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

/**
 * Cliente HTTP base pra Amazon Selling Partner API.
 */
class Client
{
    private const MAX_RETRIES_ON_THROTTLE = 3;

    public function __construct(
        private readonly MarketplaceIntegration $integration,
    ) {}

    public function orders(): Orders
    {
        return new Orders($this);
    }

    public function sellers(): \SistemAtc\Marketplaces\Amazon\Endpoints\Sellers
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\Sellers($this);
    }

    public function finances(): Finances
    {
        return new Finances($this);
    }

    public function notifications(): Notifications
    {
        return new Notifications($this);
    }

    public function definitions(): Definitions
    {
        return new Definitions($this);
    }

    public function listings(): Listings
    {
        return new Listings($this);
    }

    public function messaging(): Messaging
    {
        return new Messaging($this);
    }

    public function reports(): Reports
    {
        return new Reports($this);
    }

    public function invoices(): Invoices
    {
        return new Invoices($this);
    }

    public function tokens(): Tokens
    {
        return new Tokens($this);
    }

    public function pricing(): Pricing
    {
        return new Pricing($this);
    }

    /**
     * @param  array<string, string>  $headers  Headers extras DESTA chamada (ex.:
     *   `x-amzn-shipping-business-id`, `x-amzn-idempotency-token`,
     *   `x-amzn-fulfillment-service-id`). Nao vazam pras demais.
     */
    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->request('GET', $path, $query, headers: $headers);
    }

    /** @param  array<string, string>  $headers */
    public function post(string $path, array $body = [], array $headers = []): array
    {
        return $this->request('POST', $path, body: $body, headers: $headers);
    }

    /**
     * GET restrito: obtém um RDT pra (GET, $path) e usa no lugar do token LWA.
     */
    public function getRestricted(string $path, array $query = []): array
    {
        return $this->request('GET', $path, query: $query, tokenOverride: $this->rdtFor('GET', $path));
    }

    /**
     * POST restrito: obtém um RDT pra (POST, $path) e usa no lugar do token LWA.
     */
    public function postRestricted(string $path, array $body = []): array
    {
        return $this->request('POST', $path, body: $body, tokenOverride: $this->rdtFor('POST', $path));
    }

    private function rdtFor(string $method, string $path): ?string
    {
        $rdt = $this->tokens()->createRestrictedDataToken([
            ['method' => $method, 'path' => $path],
        ]);

        return $rdt !== '' ? $rdt : null;
    }

    /**
     * GET grantless: usa um token client_credentials (scope) no lugar do token
     * de seller. Pra operacoes SP-API que nao exigem consent de um seller
     * (ex.: Notifications getDestinations).
     */
    public function getGrantless(string $path, string $scope, array $query = []): array
    {
        return $this->request('GET', $path, query: $query, tokenOverride: TokenRefresher::grantless($this->integration, $scope));
    }

    /** POST grantless (ex.: Notifications createDestination). */
    public function postGrantless(string $path, string $scope, array $body = []): array
    {
        return $this->request('POST', $path, body: $body, tokenOverride: TokenRefresher::grantless($this->integration, $scope));
    }

    /** DELETE grantless (ex.: Notifications deleteDestination / deleteSubscriptionById). */
    public function deleteGrantless(string $path, string $scope): array
    {
        return $this->request('DELETE', $path, tokenOverride: TokenRefresher::grantless($this->integration, $scope));
    }

    /** @param  array<string, string>  $headers */
    public function put(string $path, array $body = [], array $headers = []): array
    {
        return $this->request('PUT', $path, body: $body, headers: $headers);
    }

    /** @param  array<string, string>  $headers */
    public function patch(string $path, array $body = [], array $headers = []): array
    {
        return $this->request('PATCH', $path, body: $body, headers: $headers);
    }

    /** @param  array<string, string>  $headers */
    public function delete(string $path, array $headers = []): array
    {
        return $this->request('DELETE', $path, headers: $headers);
    }

    private function request(string $method, string $path, array $query = [], array $body = [], ?string $tokenOverride = null, array $headers = []): array
    {
        // tokenOverride = RDT (Restricted Data Token) pra operações restritas;
        // senão usa o access token LWA normal.
        $token = $tokenOverride ?? TokenRefresher::refresh($this->integration);

        $settings = $this->integration->getMarketplaceSettings();
        $base = ! empty($settings['endpoint'])
            ? (string) $settings['endpoint']
            : (string) config('marketplaces.amazon.spapi_base_url', 'https://sellingpartnerapi-na.amazon.com');
        $url = rtrim($base, '/').'/'.ltrim($path, '/');

        for ($attempt = 1; $attempt <= self::MAX_RETRIES_ON_THROTTLE; $attempt++) {
            $req = $this->buildRequest($token, $headers);
            
            if ($query) {
                $urlWithQuery = $url . '?' . http_build_query($query);
            } else {
                $urlWithQuery = $url;
            }

            $resp = match ($method) {
                'GET' => $req->get($url, $query),
                'POST' => $req->post($urlWithQuery, $body),
                'PUT' => $req->put($urlWithQuery, $body),
                'PATCH' => $req->patch($urlWithQuery, $body),
                'DELETE' => $req->delete($urlWithQuery),
                default => throw new RuntimeException("HTTP method nao suportado: $method"),
            };

            if ($resp->successful()) {
                return $resp->json() ?? [];
            }

            if ($resp->status() === 429 && $attempt < self::MAX_RETRIES_ON_THROTTLE) {
                $sleep = (int) ($resp->header('Retry-After') ?: pow(2, $attempt));
                sleep($sleep);
                continue;
            }

            if ($resp->status() === 401 && $attempt === 1 && $tokenOverride === null) {
                $token = TokenRefresher::refresh($this->integration, force: true);
                continue;
            }
            
            if ($resp->status() === 404) {
                return [];
            }

            $this->handleError($resp);
        }

        return [];
    }

    /** @param  array<string, string>  $headers */
    private function buildRequest(string $token, array $headers = []): PendingRequest
    {
        return Http::withHeaders(array_merge([
            'x-amz-access-token' => $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $headers))->timeout(30);
    }

    private function handleError(Response $response): void
    {
        Log::error('Amazon API Error', [
            'status' => $response->status(),
            'body' => $response->json(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        
        throw new RuntimeException("Amazon API Error: " . $response->body(), $response->status());
    }

    public function fulfillmentInboundV0(): FulfillmentInboundV0
    {
        return new FulfillmentInboundV0($this);
    }

    public function fulfillmentInbound(): FulfillmentInbound
    {
        return new FulfillmentInbound($this);
    }

    public function fbaInboundEligibility(): FbaInboundEligibility
    {
        return new FbaInboundEligibility($this);
    }

    public function fbaInventory(): FbaInventory
    {
        return new FbaInventory($this);
    }

    public function shippingV1(): ShippingV1
    {
        return new ShippingV1($this);
    }

    public function shipping(): Shipping
    {
        return new Shipping($this);
    }

    public function merchantFulfillment(): MerchantFulfillment
    {
        return new MerchantFulfillment($this);
    }

    public function easyShip(): EasyShip
    {
        return new EasyShip($this);
    }

    public function tracking(): Tracking
    {
        return new Tracking($this);
    }

    public function supplySources(): SupplySources
    {
        return new SupplySources($this);
    }

    public function externalFulfillment(): ExternalFulfillment
    {
        return new ExternalFulfillment($this);
    }

    public function listingsRestrictions(): ListingsRestrictions
    {
        return new ListingsRestrictions($this);
    }

    public function productFees(): ProductFees
    {
        return new ProductFees($this);
    }

    public function catalogItems(): CatalogItems
    {
        return new CatalogItems($this);
    }

    public function catalogItemsLegacy(): CatalogItemsLegacy
    {
        return new CatalogItemsLegacy($this);
    }

    public function promotions(): Promotions
    {
        return new Promotions($this);
    }

    public function fulfillmentOutbound(): FulfillmentOutbound
    {
        return new FulfillmentOutbound($this);
    }

    public function fulfillmentOutbound2026(): FulfillmentOutbound2026
    {
        return new FulfillmentOutbound2026($this);
    }

    public function awd(): Awd
    {
        return new Awd($this);
    }

    public function replenishment(): Replenishment
    {
        return new Replenishment($this);
    }

    public function uploads(): Uploads
    {
        return new Uploads($this);
    }

    public function aplusContent(): AplusContent
    {
        return new AplusContent($this);
    }

    public function ordersV2026(): OrdersV2026
    {
        return new OrdersV2026($this);
    }

    public function sellerWallet(): SellerWallet
    {
        return new SellerWallet($this);
    }

    public function feeds(): Feeds
    {
        return new Feeds($this);
    }

    public function dataKiosk(): DataKiosk
    {
        return new DataKiosk($this);
    }

    public function sales(): Sales
    {
        return new Sales($this);
    }

    public function solicitations(): Solicitations
    {
        return new Solicitations($this);
    }

    public function customerFeedback(): CustomerFeedback
    {
        return new CustomerFeedback($this);
    }

    // ---- Vendor Central (1P) ------------------------------------------

    public function vendorOrders(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorOrders
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorOrders($this);
    }

    public function vendorShipments(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorShipments
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorShipments($this);
    }

    public function vendorInvoices(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorInvoices
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorInvoices($this);
    }

    public function vendorTransactionStatus(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorTransactionStatus
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorTransactionStatus($this);
    }

    public function vendorDirectFulfillmentOrdersV1(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrdersV1
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrdersV1($this);
    }

    public function vendorDirectFulfillmentOrders(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrders
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrders($this);
    }

    public function vendorDirectFulfillmentShippingV1(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShippingV1
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShippingV1($this);
    }

    public function vendorDirectFulfillmentShipping(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShipping
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShipping($this);
    }

    public function vendorDirectFulfillmentTransactionsV1(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactionsV1
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactionsV1($this);
    }

    public function vendorDirectFulfillmentTransactions(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactions
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactions($this);
    }

    public function vendorDirectFulfillmentInventory(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentInventory
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentInventory($this);
    }

    public function vendorDirectFulfillmentPayments(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentPayments
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentPayments($this);
    }

    public function vendorDirectFulfillmentSandbox(): \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentSandbox
    {
        return new \SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentSandbox($this);
    }

    public function services(): Services
    {
        return new Services($this);
    }

    public function vehicles(): Vehicles
    {
        return new Vehicles($this);
    }

    public function applicationManagement(): ApplicationManagement
    {
        return new ApplicationManagement($this);
    }

    public function applicationIntegrations(): ApplicationIntegrations
    {
        return new ApplicationIntegrations($this);
    }

    public function deliveryByAmazon(): DeliveryByAmazon
    {
        return new DeliveryByAmazon($this);
    }
}
