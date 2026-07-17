<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * `Offers[].ShippingTime` — os campos aqui são camelCase (não Pascal!) na
 * própria SP-API, então declarados com #[JsonKey].
 */
final class ShippingTime implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[JsonKey('availabilityType')]
        public readonly ?string $availabilityType = null,
        #[JsonKey('minimumHours')]
        public readonly ?int $minimumHours = null,
        #[JsonKey('maximumHours')]
        public readonly ?int $maximumHours = null,
    ) {}
}
