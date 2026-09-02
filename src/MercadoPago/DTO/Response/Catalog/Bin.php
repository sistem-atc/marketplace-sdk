<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Method BIN (Bank Identification Number) settings resource. Defines regex patterns that
 * determine which card BINs are accepted, excluded, or eligible for installments within a payment
 * method's settings.
 */
final class Bin implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Regex pattern matching accepted card BINs (first 6-8 digits of the card number). */
        public readonly ?string $pattern = null,

        /** Regex pattern matching card BINs explicitly excluded from this payment method. */
        public readonly ?string $exclusionPattern = null,

        /** Regex pattern matching card BINs eligible for installment plans. */
        public readonly ?string $installmentsPattern = null,
    ) {}
}
