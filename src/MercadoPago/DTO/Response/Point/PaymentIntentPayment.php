<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Point Payment Intent Payment configuration resource. Defines the payment parameters for a Point
 * payment intent, including the number of installments, who bears the installment cost, and the
 * payment type (e.g. "credit_card", "debit_card").
 */
final class PaymentIntentPayment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Number of installments for the payment. */
        public readonly ?int $installments = null,

        /** Cost of installments. */
        public readonly ?string $installmentsCost = null,

        /** Type of the payment. */
        public readonly ?string $type = null,
    ) {}
}
