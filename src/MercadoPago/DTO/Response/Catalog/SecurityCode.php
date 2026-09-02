<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Method Security Code settings resource. Defines the security code (CVV/CVC)
 * requirements for a payment method, including its length, input mode, and physical location on
 * the card.
 */
final class SecurityCode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Security code mode. */
        public readonly ?string $mode = null,

        /** Expected security code length (e.g., 3 for CVV, 4 for Amex CID). */
        public readonly ?int $length = null,

        /** Physical location on the card ("back" for most cards, "front" for Amex). */
        public readonly ?string $cardLocation = null,
    ) {}
}
