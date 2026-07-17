<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteúdo da mensagem (`messages[].content`) — POLIMÓRFICO por
 * `ChatMessage::$messageType`. Só o subconjunto do tipo vem preenchido:
 *
 *   text          -> text
 *   image         -> url, thumbUrl, thumbHeight, thumbWidth, fileServerId
 *   sticker       -> stickerId, stickerPackageId, imageUrl
 *   faq_liveagent -> questionId, categoryId, triggerSource, passThroughData
 *   bundle_message-> messages (lista de blocos aninhados)
 *
 * Por isso é uma UNIÃO de campos, não um bloco fixo: cheque `messageType`
 * antes de ler. Campo null aqui geralmente só quer dizer "não é desse tipo".
 *
 * @property array<int, mixed>|null $messages
 */
final class ChatMessageContent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // text
        public readonly ?string $text = null,
        // image
        public readonly ?string $url = null,
        public readonly ?string $thumbUrl = null,
        public readonly ?int $thumbHeight = null,
        public readonly ?int $thumbWidth = null,
        public readonly ?int $fileServerId = null,
        // sticker
        public readonly ?string $stickerId = null,
        public readonly ?string $stickerPackageId = null,
        public readonly ?string $imageUrl = null,
        // faq / chatbot
        public readonly ?int $questionId = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $triggerSource = null,
        public readonly ?string $passThroughData = null,
        public readonly ?bool $shopChatBotRepliedType = null,
        public readonly ?bool $shopChatBotReplied = null,
        public readonly ?int $type = null,
        // bundle_message: blocos aninhados (shape livre da Shopee).
        public readonly ?array $messages = null,
        // item/order (visto no webhook)
        public readonly ?int $itemId = null,
        public readonly ?string $orderSn = null,
    ) {}
}
