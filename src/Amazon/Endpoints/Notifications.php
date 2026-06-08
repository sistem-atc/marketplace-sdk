<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Notifications API v1 da SP-API — destinations (webhooks) e subscriptions.
 *
 * Fluxo: createDestination() → createSubscription() → getSubscription().
 * Requer role "Notifications API" no app SP-API.
 *
 * Rate limits: 1 req/s + burst 5.
 */
class Notifications
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listDestinations(): array
    {
        $resp = $this->client->get('/notifications/v1/destinations');

        return data_get($resp, 'payload', []);
    }

    /**
     * @return array<string, mixed>  Destination criado (com destinationId)
     */
    public function createDestination(string $name, string $url): array
    {
        $resp = $this->client->post('/notifications/v1/destinations', [
            'name' => $name,
            'resourceSpecification' => [
                'https' => ['url' => $url],
            ],
        ]);

        return data_get($resp, 'payload', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteDestination(string $destinationId): array
    {
        $resp = $this->client->delete('/notifications/v1/destinations/'.rawurlencode($destinationId));

        return data_get($resp, 'payload', []);
    }

    /**
     * Subscription de um tipo de evento. Retorna [] quando nao existe (404).
     *
     * @return array<string, mixed>
     */
    public function getSubscription(string $notificationType): array
    {
        $resp = $this->client->get('/notifications/v1/subscriptions/'.rawurlencode($notificationType));

        return data_get($resp, 'payload', []);
    }

    /**
     * Liga um notificationType a um destination.
     *
     * @return array<string, mixed>  Subscription criada (com subscriptionId)
     */
    public function createSubscription(string $notificationType, string $destinationId): array
    {
        $resp = $this->client->post(
            '/notifications/v1/subscriptions/'.rawurlencode($notificationType),
            [
                'payloadVersion' => '1.0',
                'destinationId'  => $destinationId,
            ]
        );

        return data_get($resp, 'payload', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteSubscription(string $notificationType, string $subscriptionId): array
    {
        $resp = $this->client->delete(
            '/notifications/v1/subscriptions/'.rawurlencode($notificationType).'/'.rawurlencode($subscriptionId)
        );

        return data_get($resp, 'payload', []);
    }
}
