<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the sequence position of a payment within a subscription plan. Tracks which
 * installment or billing cycle this payment corresponds to out of the total number of planned
 * payments.
 */
final class SubscriptionSequence implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Current payment number in the subscription series (e.g., 3 for the third payment). */
        public readonly ?int $number = null,

        /** Total number of planned payments in the subscription (null if open-ended). */
        public readonly ?int $total = null,
    ) {}
}
