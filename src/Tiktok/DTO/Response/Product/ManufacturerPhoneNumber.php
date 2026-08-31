<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Telefone do fabricante — objeto, nao string.
 *
 * `availability` = UNAVAILABLE e' um estado VALIDO (fabricante sem telefone);
 * nesse caso `countryCode`/`localNumber` nao vem. Nao trate ausencia de
 * numero como erro.
 */
final class ManufacturerPhoneNumber implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $availability = null,
        // Vem com o "+" na frente ("+353").
        public readonly ?string $countryCode = null,
        public readonly ?string $localNumber = null,
    ) {}
}
