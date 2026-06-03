<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Amazon\Endpoints\Notifications;
use SistemAtc\Marketplaces\Amazon\Support\HttpClientFactory;

class Amazon
{
    public function orders(MarketplaceIntegration $integration): Orders
    {
        return new Orders(HttpClientFactory::make($integration), $integration);
    }

    public function finances(MarketplaceIntegration $integration): Finances
    {
        return new Finances(HttpClientFactory::make($integration), $integration);
    }

    public function notifications(MarketplaceIntegration $integration): Notifications
    {
        return new Notifications(HttpClientFactory::make($integration), $integration);
    }
}
