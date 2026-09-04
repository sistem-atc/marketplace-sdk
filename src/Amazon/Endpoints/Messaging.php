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

    // -------------------------------------------------------------------
    // Ações disponíveis + tipos de mensagem restantes (v1)
    // -------------------------------------------------------------------

    /**
     * Quais mensagens o seller PODE mandar pro comprador deste pedido
     * (GET /messaging/v1/orders/{amazonOrderId}?marketplaceIds=…). Retorna
     * HAL: `_links.actions[]` (name + href) e `_embedded.actions`. Consulte
     * antes de chamar um create*: a Amazon recusa mensagem fora da lista.
     * Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @return array<string, mixed>
     */
    public function getMessagingActionsForOrder(string $amazonOrderId, array $marketplaceIds): array
    {
        return $this->client->get(
            '/messaging/v1/orders/'.rawurlencode($amazonOrderId),
            ['marketplaceIds' => implode(',', $marketplaceIds)],
        );
    }

    /**
     * Mensagem "confirmar detalhes de entrega" (POST …/messages/confirmDeliveryDetails).
     * Body: {text} (≤ 2000 chars). Rate limit: 1 req/s + burst 5. Resposta 201 vazia.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createConfirmDeliveryDetails(string $amazonOrderId, array $marketplaceIds, array $body): array
    {
        return $this->client->post($this->messagePath($amazonOrderId, 'confirmDeliveryDetails', $marketplaceIds), $body);
    }

    /**
     * Mensagem de DIVULGAÇÃO LEGAL (POST …/messages/legalDisclosure) — só
     * anexos: body {attachments: [{uploadDestinationId, fileName}]} (upload
     * antes via Uploads API). Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createLegalDisclosure(string $amazonOrderId, array $marketplaceIds, array $body): array
    {
        return $this->client->post($this->messagePath($amazonOrderId, 'legalDisclosure', $marketplaceIds), $body);
    }

    /**
     * Mensagem "confirmar detalhes do pedido" (POST …/messages/confirmOrderDetails).
     * Body: {text}. Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createConfirmOrderDetails(string $amazonOrderId, array $marketplaceIds, array $body): array
    {
        return $this->client->post($this->messagePath($amazonOrderId, 'confirmOrderDetails', $marketplaceIds), $body);
    }

    /**
     * Mensagem "confirmar detalhes do serviço" (POST …/messages/confirmServiceDetails).
     * Body: {text}. Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createConfirmServiceDetails(string $amazonOrderId, array $marketplaceIds, array $body): array
    {
        return $this->client->post($this->messagePath($amazonOrderId, 'confirmServiceDetails', $marketplaceIds), $body);
    }

    /**
     * Envia CHAVE DE ACESSO DIGITAL (POST …/messages/digitalAccessKey) —
     * produto digital: body {text, attachments?}. Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createDigitalAccessKey(string $amazonOrderId, array $marketplaceIds, array $body): array
    {
        return $this->client->post($this->messagePath($amazonOrderId, 'digitalAccessKey', $marketplaceIds), $body);
    }

    /**
     * Mensagem de PROBLEMA INESPERADO no pedido (POST …/messages/unexpectedProblem)
     * — ex.: item fora de estoque, atraso. Body: {text}. Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createUnexpectedProblem(string $amazonOrderId, array $marketplaceIds, array $body): array
    {
        return $this->client->post($this->messagePath($amazonOrderId, 'unexpectedProblem', $marketplaceIds), $body);
    }

    /**
     * Envia a NOTA FISCAL (PDF) pro comprador (POST …/messages/invoice) —
     * body {attachments: [{uploadDestinationId, fileName}]}; o PDF sobe antes
     * pela Uploads API (createUploadDestinationForResource). Caminho BR pra
     * mandar o DANFE pelo canal oficial da Amazon. Rate limit não publicado.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function sendInvoice(string $amazonOrderId, array $marketplaceIds, array $body): array
    {
        return $this->client->post($this->messagePath($amazonOrderId, 'invoice', $marketplaceIds), $body);
    }

    /**
     * Monta `/messaging/v1/orders/{id}/messages/{type}?marketplaceIds=a,b`
     * (o Client não aceita query em POST — vai no path).
     *
     * @param  list<string>  $marketplaceIds
     */
    private function messagePath(string $amazonOrderId, string $type, array $marketplaceIds): string
    {
        return '/messaging/v1/orders/'.rawurlencode($amazonOrderId).'/messages/'.$type
            .'?'.http_build_query(['marketplaceIds' => implode(',', $marketplaceIds)]);
    }
}
