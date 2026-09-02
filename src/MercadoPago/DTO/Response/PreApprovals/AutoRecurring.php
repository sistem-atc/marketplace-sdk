<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * PreApproval Auto-Recurring configuration resource. Defines the recurring billing schedule for a
 * subscription, including the charge amount, currency, billing frequency (e.g. every 1 month),
 * date range, and whether billing is proportional to the subscription start date.
 */
final class AutoRecurring implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Currency ID. */
        public readonly ?string $currencyId = null,

        /** Recurring amount. */
        public readonly ?float $transactionAmount = null,

        /** Recurring frequency. */
        public readonly ?int $frequency = null,

        /** Recurring frequency type (days or months). */
        public readonly ?string $frequencyType = null,

        /** Recurring start date. */
        public readonly ?string $startDate = null,

        /** Recurring end date. */
        public readonly ?string $endDate = null,

        /** Indicates whether billing is proportional. */
        public readonly ?bool $billingDayProportional = null,

        /** Indicates whether there is a specific billing day. */
        public readonly ?bool $hasBillingDay = null,

        /** Free trial. */
        public readonly mixed $freeTrial = null,
    ) {}
}
