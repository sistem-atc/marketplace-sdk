<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AuthorizedPayments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Invoice resource. Represents an invoice generated for a subscription (pre-approval) billing
 * cycle in MercadoPago. Each invoice corresponds to a scheduled charge attempt against the
 * subscriber's payment method, tracking its amount, status, retry attempts, and the resulting
 * payment.
 */
final class AuthorizedPaymentResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The ID of the invoice. */
        public readonly ?int $id = null,

        /** The type of invoice. */
        public readonly ?string $type = null,

        /** The date and time when the invoice was created. */
        public readonly ?string $dateCreated = null,

        /** The date and time when the invoice was last modified. */
        public readonly ?string $lastModified = null,

        /** The preapproval ID associated with the invoice. */
        public readonly ?string $preapprovalId = null,

        /** The reason for the invoice. */
        public readonly ?string $reason = null,

        /** The external reference for the invoice. */
        public readonly ?string $externalReference = null,

        /** The currency ID. */
        public readonly ?string $currencyId = null,

        /** The transaction amount. */
        public readonly ?float $transactionAmount = null,

        /** The date for the next retry attempt. */
        public readonly ?string $nextRetryDate = null,

        /** The debit date and time for the invoice. */
        public readonly ?string $debitDate = null,

        /** The payment method ID. */
        public readonly ?string $paymentMethodId = null,

        /** The retry attempt count. */
        public readonly ?int $retryAttempt = null,

        /** Status of the invoice. */
        public readonly ?string $status = null,

        /** Summarized. */
        public readonly ?string $summarized = null,

        /** Payment info. */
        public readonly ?Payment $payment = null,
    ) {}
}
