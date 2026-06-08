<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Amazon\Endpoints\Notifications;

class Amazon
{
    /**
     * Cliente SP-API com token LWA auto-refresh + retry 429/401/404 +
     * endpoint regional. Use ->get()/->post() pra paths crus ou
     * ->orders()/->finances()/->notifications() pros endpoints tipados.
     */
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
}
