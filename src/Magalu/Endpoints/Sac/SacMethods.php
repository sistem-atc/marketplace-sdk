<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Sac;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * SAC — tickets/protocolos de pos-venda (/seller/v0/tickets) e o log de
 * transacoes assincronas do SAC (/seller/v0/transactions).
 *
 * Toda escrita responde 202 com `transaction_id`; consulte getTransaction()
 * pra saber se foi aceita. Paginacao `_limit` (default 50)/`_offset`/`_sort`
 * (`campo:asc|desc`). Datas em ISO 8601 (`AAAA-MM-DDTHH:mm:ssZ`).
 *
 * E' a API real de reclamacoes — o ClaimMethods (/seller/v1/claims) nunca
 * respondeu (ver aviso naquela classe).
 */
class SacMethods extends BaseMethods
{
    /**
     * Tickets (GET /seller/v0/tickets).
     *
     * Filtros: `status`, `code`, `ticket_id`, `order__id`, `order__code`,
     * `channel__id`, `channel__alias`, `protocol`, `created_at_gte|lte`,
     * `updated_at_gte|lte`, `due_date_gte|lte`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listTickets(array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v0/tickets', $this->page($filters, $limit, $offset, $sort));
    }

    /**
     * Abre ticket (POST /seller/v0/tickets) — 202 + transaction_id.
     *
     * Body: `type` (obrigatorio: cancelamento/entrega), `order {code, delivery
     * {id, items[]}}` (obrigatorio), `channel {id, alias}`, `reason`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createTicket(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, '/seller/v0/tickets', [], $data);
    }

    /**
     * Ticket por id (GET /seller/v0/tickets/{ticket_id}).
     *
     * @return array<string, mixed>
     */
    public function getTicket(string $ticketId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v0/tickets/{$ticketId}");
    }

    /**
     * Eventos do ticket (GET /seller/v0/tickets/{id}/events).
     * Filtros: `type`, `created_at_gte|lte`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listEvents(string $ticketId, array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v0/tickets/{$ticketId}/events", $this->page($filters, $limit, $offset, $sort));
    }

    /**
     * Registra evento no ticket (POST /seller/v0/tickets/{id}/events) — 202.
     *
     * `type`: product_sent | return_info_sent | authorize_refund | product_received.
     * `code` e' livre pro ERP do seller.
     *
     * @return array<string, mixed>
     */
    public function createEvent(string $ticketId, string $type, ?string $code = null): array
    {
        $body = ['type' => $type];
        if ($code !== null) $body['code'] = $code;

        return $this->makeRequest(HttpMethod::POST, "/seller/v0/tickets/{$ticketId}/events", [], $body);
    }

    /**
     * Mensagens do ticket (GET /seller/v0/tickets/{id}/messages). Filtro `has_attachment`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listMessages(string $ticketId, array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v0/tickets/{$ticketId}/messages", $this->page($filters, $limit, $offset, $sort));
    }

    /**
     * Envia mensagem JSON (POST /seller/v0/tickets/{id}/messages) — 202.
     *
     * `destination`: customer | channel | seller. `attachments[]` sao
     * referencias ja' hospedadas; pra subir arquivo use createMessageWithFile().
     *
     * @param list<array<string, mixed>> $attachments
     * @return array<string, mixed>
     */
    public function createMessage(
        string $ticketId,
        string $message,
        string $destination,
        string $ownerCode,
        string $ownerName,
        ?string $code = null,
        array $attachments = [],
    ): array {
        $body = [
            'message' => $message,
            'destination' => $destination,
            'owner' => ['code' => $ownerCode, 'name' => $ownerName],
        ];
        if ($code !== null) $body['code'] = $code;
        if ($attachments) $body['attachments'] = array_values($attachments);

        return $this->makeRequest(HttpMethod::POST, "/seller/v0/tickets/{$ticketId}/messages", [], $body);
    }

    /**
     * Envia mensagem com anexo, multipart (POST /seller/v1/tickets/{id}/messages) — 202.
     *
     * Arquivo ate' 25 MB, JPG/JPEG/PNG/PDF. Vai direto no client (multipart
     * nao passa pelo makeRequest JSON); 4xx/5xx viram MagaluRequestException.
     *
     * @return array<string, mixed>
     */
    public function createMessageWithFile(
        string $ticketId,
        string $message,
        string $destination,
        string $ownerCode,
        string $ownerName,
        string $fileContents,
        string $fileName,
        ?string $code = null,
    ): array {
        $fields = [
            'message' => $message,
            'destination' => $destination,
            'owner_code' => $ownerCode,
            'owner_name' => $ownerName,
        ];
        if ($code !== null) $fields['code'] = $code;

        $response = $this->httpClient
            ->attach('file', $fileContents, $fileName)
            ->post("/seller/v1/tickets/{$ticketId}/messages", $fields);

        if ($response->failed()) $this->handleError($response);

        return $response->json() ?? [];
    }

    /**
     * Uma mensagem (GET /seller/v0/tickets/{id}/messages/{message_id}).
     *
     * @return array<string, mixed>
     */
    public function getMessage(string $ticketId, string $messageId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v0/tickets/{$ticketId}/messages/{$messageId}");
    }

    /**
     * Baixa o anexo da mensagem (binario cru)
     * (GET /seller/v0/tickets/{id}/messages/{message_id}/attachments/{attachment_id}).
     */
    public function downloadAttachment(string $ticketId, string $messageId, string $attachmentId): string
    {
        return $this->rawGet("/seller/v0/tickets/{$ticketId}/messages/{$messageId}/attachments/{$attachmentId}");
    }

    /**
     * Codigos de devolucao (reversa) do ticket (GET /seller/v0/tickets/{id}/returns).
     * Filtros: `reverse_code`, `valid_for_days`, `created_at_gte|lte`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listReturns(string $ticketId, array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v0/tickets/{$ticketId}/returns", $this->page($filters, $limit, $offset, $sort));
    }

    /**
     * Gera codigo de devolucao Magalu Entregas (POST /seller/v0/tickets/{id}/returns) — 202, sem body.
     *
     * @return array<string, mixed>
     */
    public function createReturn(string $ticketId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/seller/v0/tickets/{$ticketId}/returns");
    }

    /**
     * Um codigo de devolucao (GET /seller/v0/tickets/{id}/returns/{ticket_return_id}).
     *
     * @return array<string, mixed>
     */
    public function getReturn(string $ticketId, string $ticketReturnId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v0/tickets/{$ticketId}/returns/{$ticketReturnId}");
    }

    /**
     * Transacoes assincronas do SAC (GET /seller/v0/transactions).
     * `createdAtGte`/`createdAtLte` OBRIGATORIOS; `status`: pending|processing|
     * completed|failed|cancelled.
     *
     * @return array<string, mixed>
     */
    public function listTransactions(
        string $createdAtGte,
        string $createdAtLte,
        ?string $status = null,
        int $limit = 50,
        int $offset = 0,
        ?string $sort = null,
    ): array {
        $filters = ['created_at_gte' => $createdAtGte, 'created_at_lte' => $createdAtLte];
        if ($status !== null) $filters['status'] = $status;

        return $this->makeRequest(HttpMethod::GET, '/seller/v0/transactions', $this->page($filters, $limit, $offset, $sort));
    }

    /**
     * Uma transacao com seus eventos (GET /seller/v0/transactions/{transaction_id}).
     *
     * @return array<string, mixed>
     */
    public function getTransaction(string $transactionId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v0/transactions/{$transactionId}");
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    private function page(array $filters, int $limit, int $offset, ?string $sort): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $query;
    }
}
