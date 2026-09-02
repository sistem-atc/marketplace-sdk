<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Payment resource. Represents a payment associated with a merchant order. A
 * merchant order may have multiple payments (e.g. split payments, retries). Each payment tracks
 * its own amount, status, approval date, and refund information.
 */
final class Payment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment ID. */
        public readonly ?int $id = null,

        /** Product cost. */
        public readonly ?float $transactionAmount = null,

        /** Total amount paid. */
        public readonly ?float $totalPaidAmount = null,

        /** Shipping fee. */
        public readonly ?float $shippingCost = null,

        /** ID of the currency used in payment. */
        public readonly ?string $currencyId = null,

        /** Payment status. */
        public readonly ?string $status = null,

        /** @deprecated deprecated since SDK version 3.0.4. */
        public readonly ?string $statusDetails = null,

        /** Gives more detailed information on the current state or rejection cause. */
        public readonly ?string $statusDetail = null,

        /** Operation type. */
        public readonly ?string $operationType = null,

        /** Approval date. */
        public readonly ?string $dateApproved = null,

        /** Date of creation. */
        public readonly ?string $dateCreated = null,

        /** Last modified date. */
        public readonly ?string $lastModified = null,

        /** Amount refunded in this payment. */
        public readonly ?float $amountRefunded = null,
    ) {}
}
