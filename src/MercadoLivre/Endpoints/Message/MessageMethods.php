<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Message;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class MessageMethods extends BaseMethods
{
    /**
     * Busca o histórico de mensagens de um pedido (ou pack).
     */
    public function getPackMessages(string $packId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/messages/packs/{$packId}/all", $params);
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
            [], 
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
