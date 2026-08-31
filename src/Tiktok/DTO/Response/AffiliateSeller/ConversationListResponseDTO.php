<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `GET /affiliate_seller/202412/conversations`.
 *
 * Por padrao o TikTok devolve SO' o `id` de cada conversa
 * (`only_need_conversation_id=true`) — passe `false` pra vir username, avatar
 * e contadores.
 */
final class ConversationListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $hasMore = null,
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(Conversation::class)]
        public readonly ?array $conversations = null,
    ) {}
}
