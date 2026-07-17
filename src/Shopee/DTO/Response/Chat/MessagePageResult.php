<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Chat;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Paginação do get_message_list (`page_result`).
 *
 * `nextOffset` é um CURSOR (id de mensagem em nanossegundos como string),
 * NÃO um índice numérico — passe-o como `offset` na chamada seguinte.
 */
final class MessagePageResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextOffset = null,
        public readonly ?int $pageSize = null,
        public readonly ?bool $more = null,
    ) {}
}
