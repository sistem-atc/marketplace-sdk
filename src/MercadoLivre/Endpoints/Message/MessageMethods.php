<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Message;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class MessageMethods extends BaseMethods
{
    /**
     * Busca o histórico de mensagens de um pack (pós-venda).
     *
     * Path correto = /messages/packs/{pack}/sellers/{seller}?tag=post_sale.
     * (O antigo /all retorna 404 "resource not found".)
     */
    public function getPackMessages(string $packId, int|string $sellerId, array $params = []): array
    {
        $params = array_merge(['tag' => 'post_sale'], $params);

        return $this->makeRequest(HttpMethod::GET, "/messages/packs/{$packId}/sellers/{$sellerId}", $params);
    }

    /**
     * Busca UMA mensagem pelo seu id (o `resource` do webhook topic=messages).
     * Retorna `{messages: [ { id, from, to, text, message_date,
     * message_resources: [{id,name:packs},{id,name:sellers}], ... } ]}`.
     */
    public function getMessage(string $messageId, array $params = []): array
    {
        $params = array_merge(['tag' => 'post_sale'], $params);

        return $this->makeRequest(HttpMethod::GET, "/messages/{$messageId}", $params);
    }

    /**
     * Envia uma mensagem para o comprador.
     */
    public function sendToBuyer(string $packId, int|string $sellerId, string $text, array $attachments = []): array
    {
        $body = [
            'text' => $text,
            'from' => [
                'user_id' => $sellerId
            ]
        ];

        if (!empty($attachments)) {
            $body['attachments'] = $attachments;
        }

        return $this->makeRequest(
            HttpMethod::POST,
            "/messages/packs/{$packId}/sellers/{$sellerId}",
            ['tag' => 'post_sale'],
            $body
        );
    }

    /**
     * Busca mensagens nao lidas.
     */
    public function unread(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/messages/unread', $params);
    }

    /**
     * Baixa um anexo de mensagem.
     */
    public function getAttachment(string $attachmentId): string
    {
        $response = $this->httpClient->get("/messages/attachments/{$attachmentId}");
        return (string) $response->body();
    }
}
