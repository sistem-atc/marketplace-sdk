<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents automatic/recurring payment configuration for an order payment. Used in subscription
 * or recurring billing scenarios where payments are charged automatically on a schedule.
 */
final class AutomaticPayments implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identifier of the stored payment profile used for automatic charges. */
        public readonly ?string $paymentProfileId = null,

        /** Number of retry attempts allowed if the automatic charge fails. */
        public readonly ?int $retries = null,

        /** ISO 8601 date when the automatic payment is scheduled to be charged. */
        public readonly ?string $scheduleDate = null,

        /** ISO 8601 date by which the payment must be completed before it is considered overdue. */
        public readonly ?string $dueDate = null,

        /** Subscription metadata for the recurring payment. */
        public readonly ?array $subscription = null,
    ) {}
}
