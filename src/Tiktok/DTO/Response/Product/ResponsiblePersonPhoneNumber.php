<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Telefone da pessoa responsavel. Diferente do fabricante, NAO tem campo
 * `availability` — telefone e' obrigatorio aqui. So' codigo de pais da UE.
 */
final class ResponsiblePersonPhoneNumber implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $countryCode = null,
        public readonly ?string $localNumber = null,
    ) {}
}
