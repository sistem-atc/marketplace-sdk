<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `POST /affiliate_seller/202508/conversations`.
 *
 * E' IDEMPOTENTE: se ja existe conversa com o creator, devolve a mesma com
 * `isNew=false`. Por padrao (`only_need_conversation_id=true`) so' o
 * `conversationId` vem preenchido — passe `false` pra receber o resto.
 */
final class ConversationCreateResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $conversationId = null,
        // false = a conversa ja existia
        public readonly ?bool $isNew = null,
        public readonly ?string $username = null,
        // URL crua, nao embrulhada em {url}
        public readonly ?string $avatar = null,
        public readonly ?int $unreadCount = null,
        // id de IM do creator — diferente do creator_open_id
        public readonly ?string $creatorImId = null,
    ) {}
}
