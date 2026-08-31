<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.sender` do topico 14 — quem mandou a mensagem.
 *
 * ⚠️ `im_user_id` e' ID do sistema de IM: NAO da' pra cruzar com pedido, com
 * buyer do pedido nem com nenhum outro ID da API de Order. Serve so' pra
 * agrupar mensagens da mesma pessoa dentro do IM.
 *
 * Nas roles SYSTEM e ROBOT o remetente e' a propria loja.
 */
final class NewMessageSender implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** ID no IM. Nao serve pra consultar pedido. */
        public readonly ?string $imUserId = null,
        /** BUYER | SHOP | CUSTOMER_SERVICE | SYSTEM | ROBOT */
        public readonly ?string $role = null,
    ) {}
}
