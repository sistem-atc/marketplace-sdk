<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the configuration settings for a MercadoPago order. Groups payment method
 * restrictions/defaults and online checkout behavior such as redirect URLs and differential
 * pricing.
 */
final class Config implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Text shown on the buyer's credit card statement. Approximately 10 characters max depending on the card issuer. */
        public readonly ?string $statementDescriptor = null,

        /** Offline payment expiration duration in ISO 8601 format (e.g. "P1D" = 1 day). */
        public readonly ?string $defaultPaymentDueDate = null,

        /** Payment method restrictions, defaults, and installment settings. */
        public readonly ?PaymentMethodConfig $paymentMethod = null,

        /** Online checkout configuration (redirect URLs, security). */
        public readonly ?OnlineConfig $online = null,
    ) {}
}
