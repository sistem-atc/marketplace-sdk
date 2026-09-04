<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Conversation;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Chat com o cliente (/v0/conversations) — base `services.magalu.com`.
 *
 * Conversas pos-compra entre seller e comprador. Paginacao `_limit`
 * (default 10)/`_offset`. Mensagens enviadas passam por moderacao
 * (`moderation.status`).
 */
class ConversationMethods extends BaseMethods
{
    /**
     * Conversas (GET /v0/conversations).
     *
     * Filtros: `status` (default OPENED), `from_user_ref_key`,
     * `last_interaction_at_start|end`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl('/v0/conversations'), array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Uma conversa (GET /v0/conversations/{id}).
     *
     * @return array<string, mixed>
     */
    public function get(string $conversationId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/v0/conversations/{$conversationId}"));
    }

    /**
     * Mensagens da conversa (GET /v0/conversations/{id}/messages).
     *
     * @return array<string, mixed>
     */
    public function listMessages(string $conversationId, int $limit = 10, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/v0/conversations/{$conversationId}/messages"), [
            '_limit' => $limit,
            '_offset' => $offset,
        ]);
    }

    /**
     * Uma mensagem (GET /v0/conversations/{id}/messages/{message_id}).
     *
     * @return array<string, mixed>
     */
    public function getMessage(string $conversationId, string $messageId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/v0/conversations/{$conversationId}/messages/{$messageId}"));
    }

    /**
     * Responde na conversa (POST /v0/conversations/{id}/messages) — 201.
     * `owner` = atendente do seller (`external_id`, `name`); `externalId` = id
     * da mensagem no ERP (dedupe).
     *
     * @return array<string, mixed>
     */
    public function sendMessage(
        string $conversationId,
        string $content,
        string $ownerExternalId,
        string $ownerName,
        ?string $externalId = null,
    ): array {
        $body = ['content' => $content, 'owner' => ['external_id' => $ownerExternalId, 'name' => $ownerName]];
        if ($externalId !== null) $body['external_id'] = $externalId;

        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl("/v0/conversations/{$conversationId}/messages"), [], $body);
    }

    /**
     * Marca a conversa como lida pelo seller (PATCH /v0/conversations/{id}/read_by).
     *
     * @return array<string, mixed>
     */
    public function markAsRead(string $conversationId): array
    {
        return $this->makeRequest(HttpMethod::PATCH, $this->servicesUrl("/v0/conversations/{$conversationId}/read_by"));
    }
}
