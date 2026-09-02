<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the payment method (card brand/network) associated with a saved card. Provides the
 * brand name, type classification, and logo URLs for display in checkout UIs. Used as a nested
 * object within CustomerCard and CustomerCardListResult.
 */
final class PaymentMethod implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment method identifier (e.g., "visa", "master", "amex"). */
        public readonly ?string $id = null,

        /** Human-readable name of the payment method (e.g., "Visa", "Mastercard"). */
        public readonly ?string $name = null,

        /** Payment type classification (e.g., "credit_card", "debit_card", "prepaid_card"). */
        public readonly ?string $paymentTypeId = null,

        /** URL to the payment method logo image (HTTP). */
        public readonly ?string $thumbnail = null,

        /** URL to the payment method logo image over HTTPS. */
        public readonly ?string $secureThumbnail = null,
    ) {}
}
