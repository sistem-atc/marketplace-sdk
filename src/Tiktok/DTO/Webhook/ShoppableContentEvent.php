<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `data.event` do webhook TYPE 17 — o que o criador fez com o link do produto. */
final class ShoppableContentEvent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // ADD | UPDATE | REMOVE. Cuidado: esse `type` NAO e' o type code do
        // webhook (que vive no envelope), e' a acao do criador.
        public readonly ?string $type = null,
        // VIDEO (unico valor documentado; a doc fala tambem de live stream).
        public readonly ?string $scene = null,
        // ID do video curto.
        public readonly ?string $contentId = null,
    ) {}
}
