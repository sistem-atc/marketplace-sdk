<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a fee or interest charge applied to a payment method in the MercadoPago API. Used
 * for late-payment fines and interest charges on offline payment methods (e.g. boleto). Nested
 * within PaymentMethodRules.
 */
final class PaymentFee implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Fee type (e.g. "fixed", "percentage"). */
        public readonly ?string $type = null,

        /** Fee value (absolute amount or percentage depending on type). */
        public readonly ?float $value = null,
    ) {}
}
