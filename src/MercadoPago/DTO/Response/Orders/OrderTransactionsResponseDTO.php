<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the transaction container for a MercadoPago order. Groups all financial operations
 * associated with an order: payments, refunds, and chargebacks. An order may contain multiple
 * payments (split payment scenarios) and their corresponding reversals.
 */
final class OrderTransactionsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payments associated with this order. Each element maps to Payment. @var list<Payment>|null */
        #[ArrayOf(Payment::class)]
        public readonly ?array $payments = null,

        /** Refunds processed for this order's payments. Each element maps to Refund. @var list<Refund>|null */
        #[ArrayOf(Refund::class)]
        public readonly ?array $refunds = null,

        /** Chargebacks filed against this order's payments. Each element maps to Chargeback. @var list<Chargeback>|null */
        #[ArrayOf(Chargeback::class)]
        public readonly ?array $chargebacks = null,
    ) {}
}
