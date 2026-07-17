<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Prévia da última mensagem na listagem (`latest_message_content`) — só o
 * texto. Vem NULL quando a última mensagem não é de texto (imagem/sticker):
 * nesse caso o tipo está em `Conversation::$latestMessageType`.
 */
final class LatestMessageContent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $text = null,
    ) {}
}
