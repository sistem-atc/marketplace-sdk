<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Erro PARCIAL: o TikTok responde `code: 0` (sucesso HTTP/negocio) e enfia aqui
 * o que falhou item a item. Ignorar `data.errors` e' achar que aprovou tudo
 * quando so' aprovou parte.
 *
 * `code` aqui e' STRING ("98001004"), diferente do `code` int do envelope.
 */
final class ReviewError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $message = null,
    ) {}
}
