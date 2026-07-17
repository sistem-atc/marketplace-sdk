<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Paginação da listagem de conversas (`page_result`) — por CURSOR. */
final class ConversationPageResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $more = null,
        public readonly ?int $pageSize = null,
        public readonly ?ConversationCursor $nextCursor = null,
    ) {}
}
