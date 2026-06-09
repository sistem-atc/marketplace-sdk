<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Amazon\Endpoints\Notifications;
use SistemAtc\Marketplaces\Amazon\Endpoints\Listings;
use SistemAtc\Marketplaces\Amazon\Endpoints\Messaging;

class Amazon
{
    public function client(MarketplaceIntegration $integration): Client
    {
        return new Client($integration);
    }

    public function orders(MarketplaceIntegration $integration): Orders
    {
        return $this->client($integration)->orders();
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

    public function messaging(MarketplaceIntegration $integration): Messaging
    {
        return $this->client($integration)->messaging();
    }
}
