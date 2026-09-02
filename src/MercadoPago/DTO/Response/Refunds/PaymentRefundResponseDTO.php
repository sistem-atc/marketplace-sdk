<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Refunds;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Source;

/**
 * Represents a refund issued against a MercadoPago payment. Contains refund details including the
 * refunded amount, status, and the source that initiated the refund. Returned by
 * PaymentRefundClient operations (create, get, list).
 */
final class PaymentRefundResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique refund identifier assigned by MercadoPago. */
        public readonly ?int $id = null,

        /** Identifier of the original payment that was refunded. */
        public readonly ?int $paymentId = null,

        /** Amount refunded (may be partial or the full transaction amount). */
        public readonly ?float $amount = null,

        /** Adjustment applied to the refund amount (e.g. currency conversion differences). */
        public readonly ?float $adjustmentAmount = null,

        /** Current refund status (e.g. "approved", "in_process"). */
        public readonly ?string $status = null,

        /** How the refund was processed (e.g. "standard"). */
        public readonly ?string $refundMode = null,

        /** ISO 8601 timestamp when the refund was created. */
        public readonly ?string $dateCreated = null,

        /** Reason provided for issuing the refund. */
        public readonly ?string $reason = null,

        /** Unique sequence number for reconciliation with financial systems. */
        public readonly ?string $uniqueSequenceNumber = null,

        /** Actor or system that initiated the refund. */
        public readonly ?Source $source = null,

        /** Net amount returned to the payer's payment method. */
        public readonly ?int $amountRefundedToPayer = null,

        /** Breakdown details when the refund is split across multiple destinations. */
        public readonly ?array $partitionDetails = null,

        /** Classification labels attached to the refund by MercadoPago. */
        public readonly ?array $labels = null,

        /** Additional contextual data associated with the refund. */
        public readonly ?array $additionalData = null,

        /** ISO 8601 date when the refund expires if not yet processed. */
        public readonly ?string $expirationDate = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?array $metadata = null,
    ) {}
}
