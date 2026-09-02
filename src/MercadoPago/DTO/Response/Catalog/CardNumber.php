<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Method Card Number settings resource. Defines the expected card number length and
 * validation algorithm (e.g. "standard", "none") for a payment method's card number input.
 */
final class CardNumber implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Expected card number length (e.g., 16 for Visa/Mastercard, 15 for Amex). */
        public readonly ?int $length = null,

        /** Validation algorithm applied to the card number (e.g., "standard" for Luhn check). */
        public readonly ?string $validation = null,
    ) {}
}
