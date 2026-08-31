<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Mensagem nao lida do ultimo minuto.
 *
 * `unreadMessageCount` e' o total nao lido DAQUELE remetente na conversa, nao
 * um contador global.
 */
final class UnreadMessage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $conversationId = null,
        // JSON serializado dentro de string, igual ao da conversa
        public readonly ?string $content = null,
        public readonly ?string $type = null,
        public readonly ?string $senderId = null,
        public readonly ?int $unreadMessageCount = null,
    ) {}
}
