<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/** Campos BR do endereço (`ExtendedFields`) — rua/número/bairro/complemento. */
final class AddressExtendedFields implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $streetName = null,
        public readonly ?string $streetNumber = null,
        public readonly ?string $neighborhood = null,
        public readonly ?string $complement = null,
    ) {}
}
