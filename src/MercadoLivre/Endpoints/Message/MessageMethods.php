<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Message;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Message\MessagesResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class MessageMethods extends BaseMethods
{
    /**
     * Busca o histórico de mensagens de um pack (pós-venda).
     *
     * Path correto = /messages/packs/{pack}/sellers/{seller}?tag=post_sale.
     * (O antigo /all retorna 404 "resource not found".)
     */
    public function getPackMessages(string $packId, int|string $sellerId, array $params = []): MessagesResponseDTO
    {
        $params = array_merge(['tag' => 'post_sale'], $params);

        return MessagesResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/messages/packs/{$packId}/sellers/{$sellerId}", $params)
        );
    }

    /**
     * Busca UMA mensagem pelo seu id (o `resource` do webhook topic=messages).
     * Retorna `{messages: [ { id, from, to, text, message_date,
     * message_resources: [{id,name:packs},{id,name:sellers}], ... } ]}`.
     */
    public function getMessage(string $messageId, array $params = []): MessagesResponseDTO
    {
        $params = array_merge(['tag' => 'post_sale'], $params);

        return MessagesResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/messages/{$messageId}", $params)
        );
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
    public function unread(array $params = []): MessagesResponseDTO
    {
        return MessagesResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/messages/unread', $params));
    }

    /**
     * Baixa um anexo de mensagem. A doc pede `tag=post_sale&site_id=` — o
     * site_id e opcional aqui pra nao quebrar callers antigos.
     */
    public function getAttachment(string $attachmentId, ?string $siteId = null): string
    {
        $query = ['tag' => 'post_sale'];
        if ($siteId !== null) {
            $query['site_id'] = $siteId;
        }
        $response = $this->httpClient->get('/messages/attachments/'.rawurlencode($attachmentId), $query);
        return (string) $response->body();
    }

    /**
     * Sobe um anexo (multipart `file`) pra usar depois em sendToBuyer().
     * Devolve `{id}` — esse id vai em `attachments[]` da mensagem.
     */
    public function uploadAttachment(string $contents, string $filename, string $siteId = 'MLB'): array
    {
        $response = (clone $this->httpClient)
            ->attach('file', $contents, $filename)
            ->post('/messages/attachments?'.http_build_query(['tag' => 'post_sale', 'site_id' => $siteId]));

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return $response->json() ?? [];
    }

    /**
     * Opcoes ("motivos") disponiveis pra abrir conversa com o comprador antes
     * dele responder: OTHER, SEND_INVOICE_LINK, REQUEST_VARIANTS (so cross
     * docking/drop off), DELIVERY_PROMISE (so Flex), REQUEST_BILLING_INFO...
     * Traz `char_limit` e, nas opcoes tipo template, o `template_id` do POST.
     * Pack em caso de excecao devolve 400 `blocked_by_excepted_case`: use
     * sendToBuyer() direto.
     */
    public function actionGuide(int|string $packId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/messages/action_guide/packs/{$packId}", ['tag' => 'post_sale']);
    }

    /** Cotas (caps) de mensagem ainda disponiveis pro pack por opcao. */
    public function actionGuideCapsAvailable(int|string $packId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/messages/action_guide/packs/{$packId}/caps_available", ['tag' => 'post_sale']);
    }

    /**
     * Envia a primeira mensagem do pack via opcao do action guide. Opcoes tipo
     * template exigem `templateId`; OTHER exige `text` (limite = char_limit;
     * indisponivel com envio "Entregue" em varios sites). Depois que o
     * comprador responder, use sendToBuyer().
     */
    public function sendActionGuideOption(int|string $packId, string $optionId, ?string $templateId = null, ?string $text = null): array
    {
        $body = ['option_id' => $optionId];
        if ($templateId !== null) {
            $body['template_id'] = $templateId;
        }
        if ($text !== null) {
            $body['text'] = $text;
        }

        return $this->makeRequest(HttpMethod::POST, "/messages/action_guide/packs/{$packId}/option", ['tag' => 'post_sale'], $body);
    }

    /**
     * Nao lidas de UM pack: `{user_id, results: [{resource, count}]}`.
     */
    public function unreadForPack(int|string $packId, int|string $sellerId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/messages/unread/packs/{$packId}/sellers/{$sellerId}", ['tag' => 'post_sale']);
    }

    /**
     * Conversas nao lidas do usuario por papel. `role` (seller|buyer) e
     * OBRIGATORIO — sem default no ML. Resposta `{user_id, results: [{resource,
     * count}]}` (shape diferente do unread() legado, por isso array cru).
     */
    public function unreadByRole(string $role = 'seller'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/messages/unread', ['role' => $role, 'tag' => 'post_sale']);
    }
}
