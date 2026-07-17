<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Mensagem do chat (`messages[]` do get_message_list).
 *
 * `messageType` decide o que vem em `content` (ver ChatMessageContent).
 *
 * `createdTimestamp` é epoch em SEGUNDOS. Note que a paginação usa
 * `next_offset`, que é um id de mensagem em NANOsegundos — não confundir.
 */
final class ChatMessage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $messageId = null,
        public readonly ?string $conversationId = null,
        public readonly ?string $messageType = null,
        public readonly ?ChatMessageContent $content = null,
        public readonly ?int $fromId = null,
        public readonly ?int $toId = null,
        public readonly ?int $fromShopId = null,
        public readonly ?int $toShopId = null,
        public readonly ?int $createdTimestamp = null,
        public readonly ?string $region = null,
        public readonly ?string $status = null,
        public readonly ?string $source = null,
        public readonly ?int $messageOption = null,
        // `source_content` vem ora LISTA (vazia) ora OBJETO ({order_sn}/{item_id})
        // — mixed preserva os dois sem perda.
        public readonly mixed $sourceContent = null,
        // Sempre null no corpus; a Shopee não documenta a shape.
        public readonly mixed $quotedMsg = null,
    ) {}
}
