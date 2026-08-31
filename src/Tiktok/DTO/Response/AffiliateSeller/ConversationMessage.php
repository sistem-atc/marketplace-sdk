<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Mensagem na conversa. `conversationIndex` vem como STRING e ordena o
 * historico: indice maior = mensagem mais nova.
 */
final class ConversationMessage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // ordenador; maior = mais recente
        public readonly ?string $conversationIndex = null,
        public readonly ?ConversationMessageBody $messageBody = null,
    ) {}
}
