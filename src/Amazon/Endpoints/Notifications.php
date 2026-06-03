<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

class Notifications
{
    public function __construct(
        protected PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {}

    public function listDestinations(int $retryAttempt = 0): array
    {
        $response = $this->httpClient->get('/notifications/v1/destinations');
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->listDestinations($retryAttempt + 1);
        }
        return $response->json()['payload'] ?? [];
    }

    public function createDestination(string $name, string $url, int $retryAttempt = 0): array
    {
        $response = $this->httpClient->post('/notifications/v1/destinations', [
            'name' => $name,
            'resourceSpecification' => [
                'https' => ['url' => $url],
            ],
        ]);
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->createDestination($name, $url, $retryAttempt + 1);
        }
        return $response->json()['payload'] ?? [];
    }

    public function deleteDestination(string $destinationId, int $retryAttempt = 0): array
    {
        $response = $this->httpClient->delete("/notifications/v1/destinations/{$destinationId}");
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->deleteDestination($destinationId, $retryAttempt + 1);
        }
        return $response->json()['payload'] ?? [];
    }

    public function getSubscription(string $notificationType, int $retryAttempt = 0): array
    {
        $response = $this->httpClient->get("/notifications/v1/subscriptions/" . rawurlencode($notificationType));
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->getSubscription($notificationType, $retryAttempt + 1);
        }
        return $response->json()['payload'] ?? [];
    }

    public function createSubscription(string $notificationType, string $destinationId, int $retryAttempt = 0): array
    {
        $response = $this->httpClient->post("/notifications/v1/subscriptions/" . rawurlencode($notificationType), [
            'payloadVersion' => '1.0',
            'destinationId'  => $destinationId,
        ]);
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->createSubscription($notificationType, $destinationId, $retryAttempt + 1);
        }
        return $response->json()['payload'] ?? [];
    }

    public function deleteSubscription(string $notificationType, string $subscriptionId, int $retryAttempt = 0): array
    {
        $response = $this->httpClient->delete("/notifications/v1/subscriptions/" . rawurlencode($notificationType) . "/{$subscriptionId}");
        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->deleteSubscription($notificationType, $subscriptionId, $retryAttempt + 1);
        }
        return $response->json()['payload'] ?? [];
    }
}
