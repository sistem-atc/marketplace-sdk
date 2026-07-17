<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Valor monetário da Product Pricing API (`ListingPrice`, `LandedPrice`,
 * `Shipping`).
 *
 * ATENÇÃO: aqui `Amount` é NUMBER (double), diferente do Order — onde é STRING.
 * Inconsistência da própria Amazon. `mixed` preserva o tipo cru no roundtrip
 * (float truncaria um eventual int). Converta com `(float)` no consumidor.
 */
final class Money implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly mixed $amount = null,
        public readonly ?string $currencyCode = null,
    ) {}
}
