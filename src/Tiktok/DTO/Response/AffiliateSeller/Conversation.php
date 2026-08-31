<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conversa com um creator na caixa de entrada do seller.
 *
 * `creatorImId` e' o id de IM (bate com `senderId` das mensagens), NAO o
 * `creatorOpenId` usado no resto do grupo.
 */
final class Conversation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $unreadCount = null,
        public readonly ?string $username = null,
        public readonly ?string $avatar = null,
        public readonly ?string $creatorImId = null,
    ) {}
}
