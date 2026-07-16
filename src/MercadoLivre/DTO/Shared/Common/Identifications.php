<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Documentos fiscais (`identifications`) de uma parte da nota. Nem todos os
 * campos vem sempre — PJ traz cnpj/crt/ie/ie_type, PF traz cpf.
 */
final class Identifications implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cnpj = null,
        public readonly ?string $cpf = null,
        public readonly ?string $crt = null,
        public readonly ?string $ie = null,
        public readonly ?string $ieType = null,
    ) {}
}
