<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a single payment attempt within an order payment. When a payment is retried (e.g.,
 * after a decline), each try is recorded as an Attempt with its own status and payment method
 * details.
 */
final class Attempt implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of this payment attempt. */
        public readonly ?string $id = null,

        /** Outcome status of the attempt (e.g., "approved", "rejected"). */
        public readonly ?string $status = null,

        /** Granular reason for the attempt status (e.g., "cc_rejected_insufficient_amount"). */
        public readonly ?string $statusDetail = null,

        /** Payment method details used in this attempt. */
        public readonly ?PaymentMethod $paymentMethod = null,
    ) {}
}
