<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de uma mensagem.
 *
 * `content` e' uma STRING com JSON dentro (`{"content": "oi"}`), e o shape
 * muda conforme `type` — TEXT usa `content`, PRODUCT_CARD usa `product_id`,
 * IMAGE usa `url/width/height`. Mantido como string crua de proposito: o SDK
 * nao adivinha o shape; decodifique no consumidor conforme o `type`.
 *
 * `senderId` e' id de IM, nao `creatorOpenId`.
 */
final class ConversationMessageBody implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $conversationId = null,
        // TEXT | PRODUCT_CARD | TARGET_COLLABORATION_CARD | FREE_SAMPLE_CARD | IMAGE | CRM_TEXT_WITH_IMAGE_CARD | CRM_TEXT_WITH_PRODUCTS_CARD | NOTIFICATION | EMOTICONS | SYSTEM
        public readonly ?string $type = null,
        // JSON serializado DENTRO de uma string; o shape depende do type
        public readonly ?string $content = null,
        public readonly ?int $createTime = null,
        public readonly ?string $senderId = null,
    ) {}
}
