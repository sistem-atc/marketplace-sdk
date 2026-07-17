<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/sellerchat/get_conversation_list` — paginação por
 * CURSOR (`page_result.next_cursor.next_message_time_nano`).
 *
 * @property list<Conversation>|null $conversations
 */
final class ConversationListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(Conversation::class)]
        public readonly ?array $conversations = null,
        public readonly ?ConversationPageResult $pageResult = null,
    ) {}
}
