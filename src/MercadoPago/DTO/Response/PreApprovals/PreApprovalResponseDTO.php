<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * PreApproval (Subscription) resource. Represents a subscription (pre-approval) in MercadoPago. A
 * pre-approval authorizes recurring charges against a payer's payment method according to a
 * defined billing schedule. Tracks the subscription lifecycle including status, auto-recurring
 * configuration, payment method, and summarized billing history.
 */
final class PreApprovalResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Preapproval ID. */
        public readonly ?string $id = null,

        /** Payer ID. */
        public readonly ?int $payerId = null,

        /** Payer email. */
        public readonly ?string $payerEmail = null,

        /** Return URL. */
        public readonly ?string $backUrl = null,

        /** Seller ID. */
        public readonly ?int $collectorId = null,

        /** Application ID. */
        public readonly ?int $applicationId = null,

        /** Preapproval status. */
        public readonly ?string $status = null,

        /** Reason for the subscription. */
        public readonly ?string $reason = null,

        /** Preapproval reference value. */
        public readonly ?string $externalReference = null,

        /** Creation date. */
        public readonly ?string $dateCreated = null,

        /** Last modified date. */
        public readonly ?string $lastModified = null,

        /** The subscription's starting point. */
        public readonly ?string $initPoint = null,

        /** The pre-approval plan ID. */
        public readonly ?string $preapprovalPlanId = null,

        /** The details of auto-recurring. */
        public readonly ?AutoRecurring $autoRecurring = null,

        /** The summarized subscription details. */
        public readonly ?Summarized $summarized = null,

        /** The next payment date. */
        public readonly ?string $nextPaymentDate = null,

        /** The payment method ID. */
        public readonly ?string $paymentMethodId = null,

        /** The credit card ID. */
        public readonly ?string $cardId = null,

        /** First invoice offset. */
        public readonly mixed $firstInvoiceOffset = null,

        /** Payer first name. */
        public readonly ?string $payerFirstName = null,

        /** Payer last name. */
        public readonly ?string $payerLastName = null,
    ) {}
}
