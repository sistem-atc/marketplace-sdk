<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Notification\Destination;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Notification\Subscription;

/**
 * Notifications API v1 da SP-API — destinations e subscriptions.
 *
 * IMPORTANTE (canais de entrega): a SP-API NAO entrega em webhook HTTPS. O
 * `createDestination` so' aceita **Amazon SQS** ou **Amazon EventBridge** no
 * resourceSpecification. Pra receber num endpoint HTTPS proprio, o caminho e'
 * EventBridge -> API Destination (ou SQS -> poller).
 *
 * Autorizacao por operacao (ref. doc oficial "grantless operations"):
 *   GRANTLESS (grant_type=client_credentials, scope=sellingpartnerapi::notifications):
 *     getDestinations, createDestination, getDestination, deleteDestination,
 *     getSubscriptionById, deleteSubscriptionById
 *   SELLER (token autorizado / refresh_token):
 *     getSubscription, createSubscription
 *
 * Fluxo: createDestination() (grantless) -> createSubscription() (seller).
 * Requer a role "Notifications API" no app SP-API.
 *
 * Rate limits: 1 req/s + burst 5.
 */
class Notifications
{
    /** Scope LWA das operacoes grantless de Notifications. */
    public const GRANTLESS_SCOPE = 'sellingpartnerapi::notifications';

    public function __construct(
        private readonly Client $client,
    ) {}

    /* ---------------------------------------------------------------------
     * DESTINATIONS (grantless)
     * -------------------------------------------------------------------*/

    /**
     * @return array<int, array<string, mixed>>
     */
    /** @return list<Destination> */
    public function listDestinations(): array
    {
        $resp = $this->client->getGrantless('/notifications/v1/destinations', self::GRANTLESS_SCOPE);

        return array_map(
            static fn (array $d): Destination => Destination::fromArray($d),
            data_get($resp, 'payload', []),
        );
    }

    /** @return array<string, mixed> */
    public function getDestination(string $destinationId): array
    {
        $resp = $this->client->getGrantless(
            '/notifications/v1/destinations/'.rawurlencode($destinationId),
            self::GRANTLESS_SCOPE,
        );

        return data_get($resp, 'payload', []);
    }

    /**
     * Cria um destination generico. Passe o resourceSpecification pronto
     * (sqs OU eventBridge). Prefira os helpers createSqsDestination /
     * createEventBridgeDestination.
     *
     * @param  array<string, mixed>  $resourceSpecification
     * @return array<string, mixed>  Destination criado (com destinationId)
     */
    public function createDestination(string $name, array $resourceSpecification): array
    {
        $resp = $this->client->postGrantless(
            '/notifications/v1/destinations',
            self::GRANTLESS_SCOPE,
            [
                'name'                  => $name,
                'resourceSpecification' => $resourceSpecification,
            ],
        );

        return data_get($resp, 'payload', []);
    }

    /**
     * Destination Amazon SQS. $arn = ARN da fila (a policy da fila precisa
     * permitir o principal da SP-API enviar mensagens).
     *
     * @return array<string, mixed>
     */
    public function createSqsDestination(string $name, string $arn): array
    {
        return $this->createDestination($name, ['sqs' => ['arn' => $arn]]);
    }

    /**
     * Destination Amazon EventBridge. Cria/associa um partner event source
     * na conta AWS informada. Depois e' preciso ativar o event bus no console
     * do EventBridge.
     *
     * @return array<string, mixed>
     */
    public function createEventBridgeDestination(string $name, string $accountId, string $region): array
    {
        return $this->createDestination($name, [
            'eventBridge' => ['accountId' => $accountId, 'region' => $region],
        ]);
    }

    /** @return array<string, mixed> */
    public function deleteDestination(string $destinationId): array
    {
        $resp = $this->client->deleteGrantless(
            '/notifications/v1/destinations/'.rawurlencode($destinationId),
            self::GRANTLESS_SCOPE,
        );

        return data_get($resp, 'payload', []);
    }

    /* ---------------------------------------------------------------------
     * SUBSCRIPTIONS
     * -------------------------------------------------------------------*/

    /**
     * Subscription ativa de um tipo de evento (SELLER token). Retorna []
     * quando nao existe (404).
     *
     * @return array<string, mixed>
     */
    public function getSubscription(string $notificationType): Subscription
    {
        $resp = $this->client->get('/notifications/v1/subscriptions/'.rawurlencode($notificationType));

        return Subscription::fromArray(data_get($resp, 'payload', []));
    }

    /**
     * Liga um notificationType a um destination (SELLER token).
     *
     * @return array<string, mixed>  Subscription criada (com subscriptionId)
     */
    public function createSubscription(string $notificationType, string $destinationId, string $payloadVersion = '1.0'): array
    {
        $resp = $this->client->post(
            '/notifications/v1/subscriptions/'.rawurlencode($notificationType),
            [
                'payloadVersion' => $payloadVersion,
                'destinationId'  => $destinationId,
            ],
        );

        return data_get($resp, 'payload', []);
    }

    /**
     * Detalhe de uma subscription por id (GRANTLESS).
     *
     * @return array<string, mixed>
     */
    public function getSubscriptionById(string $notificationType, string $subscriptionId): array
    {
        $resp = $this->client->getGrantless(
            '/notifications/v1/subscriptions/'.rawurlencode($notificationType).'/'.rawurlencode($subscriptionId),
            self::GRANTLESS_SCOPE,
        );

        return data_get($resp, 'payload', []);
    }

    /**
     * Remove uma subscription por id (GRANTLESS — deleteSubscriptionById).
     *
     * @return array<string, mixed>
     */
    public function deleteSubscription(string $notificationType, string $subscriptionId): array
    {
        $resp = $this->client->deleteGrantless(
            '/notifications/v1/subscriptions/'.rawurlencode($notificationType).'/'.rawurlencode($subscriptionId),
            self::GRANTLESS_SCOPE,
        );

        return data_get($resp, 'payload', []);
    }
}
