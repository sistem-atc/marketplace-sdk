<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Disbursement Refund List resource. Represents the list of disbursement refunds returned when
 * listing all refunds for an advanced payment via `GET /v1/advanced_payments/{id}/refunds`.
 */
final class DisbursementRefundListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** List of disbursement refunds. @var list<DisbursementRefundResponseDTO>|null */
        #[ArrayOf(DisbursementRefundResponseDTO::class)]
        public readonly ?array $refunds = null,
    ) {}
}
