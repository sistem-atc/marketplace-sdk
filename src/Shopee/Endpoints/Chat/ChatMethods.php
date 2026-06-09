<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Chat;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ChatMethods extends BaseMethods
{
    public function getConversationList(int $pageSize = 20, string $cursor = ''): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/sellerchat/get_conversation_list', [
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ]);
    }

    public function getMessageList(int $conversationId, int $pageSize = 20, string $cursor = ''): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/sellerchat/get_message_list', [
            'conversation_id' => $conversationId,
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ]);
    }

    public function sendMessage(int $conversationId, string $text): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/sellerchat/send_message', [], [
            'conversation_id' => $conversationId,
            'message_type' => 'TEXT',
            'content' => [
                'text' => $text,
            ],
        ]);
    }
}
