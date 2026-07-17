<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Message;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ das respostas de mensagens (`getPackMessages` / `getMessage` / `unread`).
 * `conversationStatus` diz se o chat está bloqueado/disponível.
 *
 * @property list<Message> $messages
 */
final class MessagesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<Message> $messages */
    public function __construct(
        #[ArrayOf(Message::class)]
        public readonly array $messages = [],
        public readonly mixed $paging = null,
        public readonly mixed $conversationStatus = null,
        public readonly ?int $sellerMaxMessageLength = null,
        public readonly ?int $buyerMaxMessageLength = null,
    ) {}
}
