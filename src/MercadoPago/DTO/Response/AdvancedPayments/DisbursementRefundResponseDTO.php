<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Disbursement Refund resource. Represents a refund on a disbursement within an advanced (split)
 * payment.
 */
final class DisbursementRefundResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The disbursement refund ID. */
        public readonly ?int $id = null,

        /** The advanced payment ID this refund belongs to. */
        public readonly ?int $advancedPaymentId = null,

        /** The disbursement ID that was refunded. */
        public readonly ?int $disbursementId = null,

        /** The refunded amount. */
        public readonly ?float $amount = null,

        /** The current status of the refund. */
        public readonly ?string $status = null,

        /** The date and time when the refund was created. */
        public readonly ?string $dateCreated = null,
    ) {}
}
