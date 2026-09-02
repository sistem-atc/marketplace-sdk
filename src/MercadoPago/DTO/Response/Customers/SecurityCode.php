<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Describes the security code (CVV/CVC) configuration for a payment card. Indicates how many
 * digits the security code has and where it is physically located on the card. Used as a nested
 * object within CustomerCard and CustomerCardListResult.
 */
final class SecurityCode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Number of digits in the security code (typically 3 or 4). */
        public readonly ?int $length = null,

        /** Physical location of the security code on the card (e.g., "back", "front"). */
        public readonly ?string $cardLocation = null,
    ) {}
}
