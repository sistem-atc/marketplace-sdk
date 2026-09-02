<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovalPlans;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * PreApproval Plan (Subscription Plan) resource. Represents a subscription plan template in
 * MercadoPago. A plan defines the recurring billing rules (frequency, amount, currency) and
 * allowed payment methods that subscribers will follow when they create a subscription from this
 * plan.
 */
final class PreApprovalPlanResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Subscription ID. */
        public readonly ?string $id = null,

        /** Return URL. */
        public readonly ?string $backUrl = null,

        /** Collector ID. */
        public readonly ?int $collectorId = null,

        /** Application ID. */
        public readonly ?int $applicationId = null,

        /** Reason for the subscription. */
        public readonly ?string $reason = null,

        /** Subscription status. */
        public readonly ?string $status = null,

        /** Date of creation. */
        public readonly ?string $dateCreated = null,

        /** Date of last modification. */
        public readonly ?string $lastModified = null,

        /** Initial point. */
        public readonly ?string $initPoint = null,

        /** Auto-recurring subscription details. */
        public readonly ?AutoRecurring $autoRecurring = null,

        /** Allowed payment methods. */
        public readonly ?PaymentMethodsAllowed $paymentMethodsAllowed = null,

        /** Subscribed. */
        public readonly ?int $subscribed = null,
    ) {}
}
