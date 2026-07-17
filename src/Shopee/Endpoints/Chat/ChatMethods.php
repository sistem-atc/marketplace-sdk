<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Chat;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Chat\Conversation;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Chat\ConversationListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Chat\MessageListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Chat\UnreadCountResponseDTO;

/**
 * Shopee sellerchat API v2 (`/api/v2/sellerchat/...`) — chat buyer<->shop.
 *
 * Conversa e' por COMPRADOR (`to_id`), nao por pedido; o vinculo com pedido e'
 * por mensagem (`message_type=order` -> `order_sn` no content, ou `source`/
 * `source_content` quando o chat nasce de um pedido/item).
 *
 * makeRequest ja' assina (partner_id/timestamp/access_token/shop_id/sign).
 */
class ChatMethods extends BaseMethods
{
    /**
     * Lista conversas. `response.conversations[]`: conversation_id, to_id
     * (comprador), to_name/to_avatar, unread_count, latest_message_*,
     * last_message_timestamp.
     *
     * @param  string  $direction  latest | older
     * @param  string  $type       all | unread | pinned
     */
    public function getConversationList(
        string $direction = 'latest',
        string $type = 'all',
        int $pageSize = 25,
        string $nextTimestampNano = '',
    ): ConversationListResponseDTO {
        $query = [
            'direction' => $direction,
            'type' => $type,
            'page_size' => min($pageSize, 25),
        ];
        if ($nextTimestampNano !== '') {
            $query['next_timestamp_nano'] = $nextTimestampNano;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sellerchat/get_conversation_list', $query);

        return ConversationListResponseDTO::fromArray($response['response'] ?? []);
    }

    /** Detalhe de UMA conversa — mesma shape do item da listagem. */
    public function getOneConversation(int|string $conversationId): Conversation
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sellerchat/get_one_conversation', [
            'conversation_id' => $conversationId,
        ]);

        return Conversation::fromArray($response['response'] ?? []);
    }

    /**
     * Mensagens de uma conversa (paginacao por OFFSET, nao cursor).
     * `->messages[]` traz content POLIMORFICO por message_type (ver
     * ChatMessageContent).
     */
    public function getMessageList(int|string $conversationId, int $pageSize = 60, int|string $offset = ''): MessageListResponseDTO
    {
        // `offset` na sellerchat e' um CURSOR (message_id_str da pagina anterior),
        // NAO um indice numerico. Vazio = pagina mais recente. Mandar offset=0
        // (int) dispara `error_not_found` — so' incluimos quando ha' cursor real.
        $params = [
            'conversation_id' => $conversationId,
            'page_size' => min($pageSize, 60),
        ];

        if ($offset !== '' && $offset !== 0 && $offset !== '0') {
            $params['offset'] = (string) $offset;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sellerchat/get_message', $params);

        return MessageListResponseDTO::fromArray($response['response'] ?? []);
    }

    /**
     * Envia mensagem ao comprador. `$toId` = user_id do COMPRADOR (do
     * get_conversation_list), NAO o conversation_id.
     *
     * @param  string  $messageType  text | sticker | image | item | order
     * @param  array<string, mixed>  $content  varia por tipo:
     *   text -> ['text' => '...']; item -> ['item_id'=>, 'shop_id'=>];
     *   order -> ['order_sn' => '...']; image -> ['image_url' => '...'];
     *   sticker -> ['sticker_id'=>, 'sticker_package_id'=>]
     *
     * NAO tipado de proposito: e' POST que envia mensagem REAL ao comprador —
     * nao da pra validar o retorno sem mandar de verdade. Chutar a shape seria
     * pior que array. Mesmo criterio do ML messages()->sendToBuyer().
     *
     * @return array<string, mixed>
     */
    public function sendMessage(int|string $toId, string $messageType, array $content): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/sellerchat/send_message', [], [
            'to_id' => (int) $toId,
            'message_type' => $messageType,
            'content' => $content,
        ]);
    }

    /** Atalho: envia texto ao comprador. @return array<string, mixed> */
    public function sendText(int|string $toId, string $text): array
    {
        return $this->sendMessage($toId, 'text', ['text' => $text]);
    }

    /** Atalho: referencia um pedido no chat. @return array<string, mixed> */
    public function sendOrder(int|string $toId, string $orderSn): array
    {
        return $this->sendMessage($toId, 'order', ['order_sn' => $orderSn]);
    }

    /**
     * Marca a conversa como lida ate' a ultima mensagem.
     *
     * @return array<string, mixed>
     */
    public function readConversation(int|string $conversationId, int|string $lastReadMessageId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/sellerchat/read_conversation', [], [
            'conversation_id' => $conversationId,
            'last_read_message_id' => (string) $lastReadMessageId,
        ]);
    }

    /** Total de conversas nao lidas. */
    public function getUnreadConversationCount(): UnreadCountResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sellerchat/get_unread_conversation_count');

        return UnreadCountResponseDTO::fromArray($response['response'] ?? []);
    }
}
