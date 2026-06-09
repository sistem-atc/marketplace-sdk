<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Messaging API v1.
 */
class Messaging
{
    public function __construct(
        private readonly Client $client,
    ) {}

    public function getAttributes(string $amazonOrderId): array
    {
        return $this->client->get("/messaging/v1/orders/{$amazonOrderId}/attributes");
    }

    public function confirmCustomizationDetails(string $amazonOrderId, array $body): array
    {
        return $this->client->post("/messaging/v1/orders/{$amazonOrderId}/messages/confirmCustomizationDetails", $body);
    }

    public function createAmazonHomeServiceAppointmentRescheduling(string $amazonOrderId, array $body): array
    {
        return $this->client->post("/messaging/v1/orders/{$amazonOrderId}/messages/amazonHomeServiceAppointmentRescheduling", $body);
    }

    public function createWarranty(string $amazonOrderId, array $body): array
    {
        return $this->client->post("/messaging/v1/orders/{$amazonOrderId}/messages/warranty", $body);
    }
}
