<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ficha da pessoa responsavel em UM idioma/pais da UE.
 * Sem `registeredTradeName` (isso e' so' de fabricante).
 */
final class ResponsiblePersonRegionalProfile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $locale = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?ResponsiblePersonPhoneNumber $phoneNumber = null,
        public readonly ?ResponsiblePersonAddress $address = null,
    ) {}
}
