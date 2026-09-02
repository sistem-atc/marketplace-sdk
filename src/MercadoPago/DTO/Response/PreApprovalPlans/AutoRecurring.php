<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovalPlans;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * PreApproval Plan Auto-Recurring configuration resource. Defines the recurring billing rules for
 * a subscription plan, including charge frequency, amount, currency, maximum repetitions, billing
 * day, proportional billing settings, and optional free trial period.
 */
final class AutoRecurring implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Frequency. */
        public readonly ?int $frequency = null,

        /** Frequency type. */
        public readonly ?string $frequencyType = null,

        /** Transaction amount. */
        public readonly ?float $transactionAmount = null,

        /** Currency ID. */
        public readonly ?string $currencyId = null,

        /** Number of repetitions. */
        public readonly ?int $repetitions = null,

        /** Free trial details. */
        public readonly ?FreeTrial $freeTrial = null,

        /** Billing day. */
        public readonly ?int $billingDay = null,

        /** Billing day proportional. */
        public readonly ?bool $billingDayProportional = null,

        /** Transaction amount proportional. */
        public readonly ?float $transactionAmountProportional = null,
    ) {}
}
