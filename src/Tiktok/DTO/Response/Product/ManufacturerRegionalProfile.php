<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ficha do fabricante em UM idioma/pais da UE (`regional_profiles[]`).
 *
 * O mesmo fabricante tem uma ficha por locale suportado — o registro nao
 * tem nome "canonico": escolha o perfil pelo locale do mercado.
 * `address` aqui e' string unica (diferente do responsavel, que e' objeto).
 */
final class ManufacturerRegionalProfile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $locale = null,
        public readonly ?string $name = null,
        public readonly ?string $registeredTradeName = null,
        public readonly ?string $email = null,
        public readonly ?string $address = null,
        public readonly ?ManufacturerPhoneNumber $phoneNumber = null,
    ) {}
}
