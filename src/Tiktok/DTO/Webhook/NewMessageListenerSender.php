<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.sender` do topico 33.
 *
 * ⚠️ A chave e' `sender_im_user_id` (nao `im_user_id`, como no topico 14) e o
 * objeto NAO tem `role` — no 33 o remetente e' sempre o criador. Dois topicos
 * de mensagem, dois shapes de sender: nao reuse o DTO do 14 aqui.
 */
final class NewMessageListenerSender implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** ID no sistema de IM. Nao serve pra consultar pedido. */
        public readonly ?string $senderImUserId = null,
    ) {}
}
