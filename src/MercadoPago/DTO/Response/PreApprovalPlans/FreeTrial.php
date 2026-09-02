<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovalPlans;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * PreApproval Plan Free Trial resource. Defines the free trial period for a subscription plan.
 * During this period, the subscriber is not charged. Specifies how long the trial lasts via
 * frequency and frequency_type, and when the first invoice is generated.
 */
final class FreeTrial implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Frequency. */
        public readonly ?int $frequency = null,

        /** Frequency type. */
        public readonly ?string $frequencyType = null,

        /** First invoice offset. */
        public readonly ?int $firstInvoiceOffset = null,
    ) {}
}
