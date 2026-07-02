<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\CustomerService;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * TikTok Shop Customer Service / IM API (versao /customer_service/202309/...).
 *
 * Chat buyer<->shop (pre-venda inclusive; NAO e' por pedido). Identidade do
 * comprador vem dos `participants[]` da lista de conversas (role BUYER/SELLER +
 * nickname) — o webhook type 33 so' entrega `sender_im_user_id`. Vinculo com
 * pedido e' por mensagem (ORDER_CARD -> order_id; PRODUCT_CARD -> product_id).
 *
 * makeRequest ja' assina (app_key/sign/shop_cipher/timestamp) + retry + refresh.
 */
class CustomerServiceMethods extends BaseMethods
{
    private const DEFAULT_VERSION = '202309';

    /**
     * Lista conversas. Resposta em `data.conversations[]` (id, unread_count,
     * latest_message, participants[]{role, im_user_id, nickname, avatar}).
     *
     * @return array<string, mixed>
     */
    public function getConversations(
        int $pageSize = 20,
        string $pageToken = '',
        string $locale = 'pt-BR',
        string $version = self::DEFAULT_VERSION,
    ): array {
        $query = ['page_size' => $pageSize, 'locale' => $locale];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        return $this->makeRequest(HttpMethod::GET, "/customer_service/{$version}/conversations", $query);
    }

    /**
     * Historico de mensagens de uma conversa. `data.messages[]` (id, type,
     * content, create_time, sender{im_user_id, role, nickname}).
     *
     * @return array<string, mixed>
     */
    public function getConversationMessages(
        string $conversationId,
        int $pageSize = 20,
        string $pageToken = '',
        string $sortOrder = 'DESC',
        string $locale = 'pt-BR',
        string $version = self::DEFAULT_VERSION,
    ): array {
        $query = ['page_size' => $pageSize, 'sort_order' => $sortOrder, 'locale' => $locale];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        return $this->makeRequest(HttpMethod::GET, "/customer_service/{$version}/conversations/{$conversationId}/messages", $query);
    }

    /**
     * Envia mensagem numa conversa.
     *
     * @param  string  $type  TEXT | IMAGE | PRODUCT_CARD | ORDER_CARD
     * @param  string  $content  pra TEXT = JSON {"content":"texto"}; pra IMAGE = id do upload
     * @return array<string, mixed>
     */
    public function sendMessage(
        string $conversationId,
        string $type,
        string $content,
        string $version = self::DEFAULT_VERSION,
    ): array {
        return $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/conversations/{$conversationId}/messages",
            [],
            ['type' => $type, 'content' => $content],
        );
    }

    /**
     * Atalho pra enviar texto (embrulha no content JSON esperado).
     *
     * @return array<string, mixed>
     */
    public function sendText(string $conversationId, string $text, string $version = self::DEFAULT_VERSION): array
    {
        return $this->sendMessage($conversationId, 'TEXT', json_encode(['content' => $text]), $version);
    }

    /**
     * Cria/abre uma conversa com um comprador. Retorna o conversation_id.
     *
     * @return array<string, mixed>
     */
    public function createConversation(string $buyerUserId, string $version = self::DEFAULT_VERSION): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/conversations",
            [],
            ['buyer_user_id' => $buyerUserId],
        );
    }

    /**
     * Marca as mensagens da conversa como lidas.
     *
     * @return array<string, mixed>
     */
    public function readMessages(string $conversationId, string $version = self::DEFAULT_VERSION): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/conversations/{$conversationId}/messages/read",
            [],
            [],
        );
    }
}
