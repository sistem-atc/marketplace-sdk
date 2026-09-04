<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * App Integrations API 2024-04-01 — notificações da NOSSA aplicação exibidas
 * ao seller no painel do Appstore do Seller Central (templates onboardados
 * no Developer Console).
 *
 * Autorização: token de SELLER (LWA normal). O modelo não declara
 * `x-amzn-api-sandbox`/security e a doc oficial de grantless operations lista
 * só Notifications (destinations) e Application Management — App Integrations
 * NÃO é grantless: a notificação é entregue ao seller que autorizou a app,
 * identificado pelo token. Rate limit de todas as operações: 1 req/s + burst 5.
 */
class ApplicationIntegrations
{
    private const BASE = '/appIntegrations/2024-04-01';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Cria uma notificação pro seller (POST /appIntegrations/2024-04-01/notifications).
     * Body CreateNotificationRequest: `templateId` (obrigatório),
     * `notificationParameters` (objeto com os placeholders do template,
     * obrigatório), `marketplaceId` (opcional). Retorna `notificationId`.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createNotification(array $body): array
    {
        return $this->client->post(self::BASE.'/notifications', $body);
    }

    /**
     * Remove notificações da nossa app do painel do seller
     * (POST /appIntegrations/2024-04-01/notifications/deletion). Body
     * DeleteNotificationsRequest: `templateId` + `deletionReason`
     * (INCORRECT_CONTENT|INCORRECT_RECIPIENT). Resposta 204 vazia.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function deleteNotifications(array $body): array
    {
        return $this->client->post(self::BASE.'/notifications/deletion', $body);
    }

    /**
     * Registra a resposta do seller a uma notificação
     * (POST /appIntegrations/2024-04-01/notifications/{notificationId}/feedback).
     * Body RecordActionFeedbackRequest: `feedbackActionCode`
     * (SELLER_ACTION_COMPLETED). Resposta 204 vazia.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function recordActionFeedback(string $notificationId, array $body): array
    {
        return $this->client->post(self::BASE.'/notifications/'.rawurlencode($notificationId).'/feedback', $body);
    }
}
