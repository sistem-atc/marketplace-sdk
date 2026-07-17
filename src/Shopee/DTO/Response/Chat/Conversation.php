<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conversa do seller chat — item de `conversations[]` e resposta inteira do
 * `get_one_conversation` (mesma shape).
 *
 * `toId`/`toName` são o COMPRADOR (a contraparte), `shopId` é a loja.
 * `lastMessageTimestamp` é epoch em SEGUNDOS.
 */
final class Conversation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $conversationId = null,
        public readonly ?int $toId = null,
        public readonly ?string $toName = null,
        public readonly ?string $toAvatar = null,
        public readonly ?int $shopId = null,
        public readonly ?int $unreadCount = null,
        public readonly ?bool $pinned = null,
        public readonly ?bool $mute = null,
        public readonly ?string $lastReadMessageId = null,
        public readonly ?string $latestMessageId = null,
        public readonly ?string $latestMessageType = null,
        public readonly ?LatestMessageContent $latestMessageContent = null,
        public readonly ?int $latestMessageFromId = null,
        public readonly ?int $lastMessageTimestamp = null,
        public readonly ?int $lastMessageOption = null,
        public readonly ?string $maxGeneralOptionHideTime = null,
        public readonly ?string $oppositeLastDeliverMsgId = null,
        public readonly ?string $oppositeLastReadMsgId = null,
    ) {}
}
