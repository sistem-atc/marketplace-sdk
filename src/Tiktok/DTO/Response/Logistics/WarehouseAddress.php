<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Endereço do armazém (`warehouses[].address`).
 *
 * ARMADILHA BR: as `addressLine*` NÃO seguem a ordem intuitiva. No Brasil o
 * TikTok usa line1 = bairro/distrito, line2 = logradouro, line3 = número
 * (`"s/n"` significa sem número) e line4 = complemento. Montar endereço
 * concatenando na ordem numérica produz lixo — use `fullAddress` ou respeite
 * esse mapa.
 *
 * `distict` é ERRO DE GRAFIA da própria API (não "district"); manter como está,
 * senão o campo é descartado na hidratação.
 *
 * Os 4 campos de nome kanji/kana só existem no mercado JP.
 */
final class WarehouseAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $region = null,
        public readonly ?string $state = null,
        public readonly ?string $city = null,
        public readonly ?string $distict = null,
        public readonly ?string $town = null,
        public readonly ?string $contactPerson = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $firstNameLocalScript = null,
        public readonly ?string $lastNameLocalScript = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $fullAddress = null,
        public readonly ?string $regionCode = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $addressLine1 = null,
        public readonly ?string $addressLine2 = null,
        public readonly ?string $addressLine3 = null,
        public readonly ?string $addressLine4 = null,
        public readonly ?Geolocation $geolocation = null,
    ) {}
}
