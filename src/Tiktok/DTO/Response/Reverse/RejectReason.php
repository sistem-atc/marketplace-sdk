<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Motivo de rejeicao disponivel pro seller.
 *
 * `name` e' a CHAVE que volta pra API na rejeicao; `text` e' so' o rotulo
 * traduzido pelo `locale` da requisicao. Nunca mande o `text` de volta.
 */
final class RejectReason implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $text = null,
    ) {}
}
