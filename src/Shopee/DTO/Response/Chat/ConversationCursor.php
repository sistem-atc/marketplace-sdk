<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Cursor da listagem de conversas (`page_result.next_cursor`).
 * `nextMessageTimeNano` vai no parâmetro `next_timestamp_nano` da chamada
 * seguinte — é NANOssegundos como string, não epoch em segundos.
 */
final class ConversationCursor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $conversationId = null,
        public readonly ?string $nextMessageTimeNano = null,
    ) {}
}
