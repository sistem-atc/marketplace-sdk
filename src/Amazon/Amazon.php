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

class Amazon
{
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
}
