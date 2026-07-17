<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Endereço (`ShippingAddress`, `DefaultShipFromLocationAddress`).
 *
 * O ShippingAddress da SP-API vem MASCARADO/parcial na maioria (só City/
 * PostalCode/StateOrRegion) — o endereço completo só via RDT (Restricted Data
 * Token) ou pela NFe. `ExtendedFields` traz os campos BR (rua/número/bairro).
 */
final class Address implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $addressLine1 = null,
        public readonly ?string $addressLine2 = null,
        public readonly ?string $addressLine3 = null,
        public readonly ?string $city = null,
        public readonly ?string $county = null,
        public readonly ?string $stateOrRegion = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $countryCode = null,
        public readonly ?AddressExtendedFields $extendedFields = null,
    ) {}
}
