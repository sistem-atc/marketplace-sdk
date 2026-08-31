<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `GET /affiliate_seller/202412/conversation/{conversation_id}/messages`.
 *
 * O path usa `conversation` no SINGULAR — enviar mensagem usa o plural.
 * Max 20 por pagina.
 */
final class ConversationMessageListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $hasMore = null,
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(ConversationMessage::class)]
        public readonly ?array $messages = null,
    ) {}
}
