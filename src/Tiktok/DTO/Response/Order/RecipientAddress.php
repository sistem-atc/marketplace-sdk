<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Endereço de entrega (`recipient_address`).
 *
 * Cidade/estado NÃO têm campo próprio — vêm em `districtInfo[]` por nível
 * (ver DistrictInfo). `addressLine1..4` são pedaços livres; `fullAddress` é
 * o texto montado.
 *
 * @property list<DistrictInfo>|null $districtInfo
 */
final class RecipientAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $firstNameLocalScript = null,
        public readonly ?string $lastNameLocalScript = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $fullAddress = null,
        public readonly ?string $addressDetail = null,
        public readonly ?string $addressLine1 = null,
        public readonly ?string $addressLine2 = null,
        public readonly ?string $addressLine3 = null,
        public readonly ?string $addressLine4 = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $regionCode = null,
        #[ArrayOf(DistrictInfo::class)]
        public readonly ?array $districtInfo = null,
        public readonly ?DeliveryPreferences $deliveryPreferences = null,
    ) {}
}
