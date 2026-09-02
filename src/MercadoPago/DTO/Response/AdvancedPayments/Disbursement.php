<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Disbursement sub-resource within an Advanced Payment. Represents a single receiver (collector)
 * that receives funds when an advanced (split) payment is captured.
 */
final class Disbursement implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The disbursement ID. */
        public readonly ?int $id = null,

        /** The collector (receiver) ID. */
        public readonly ?int $collectorId = null,

        /** The amount to disburse to this collector. */
        public readonly ?float $amount = null,

        /** The external reference for this disbursement. */
        public readonly ?string $externalReference = null,

        /** The application fee retained by the marketplace. */
        public readonly ?float $applicationFee = null,

        /** The money release date for this disbursement. */
        public readonly ?string $moneyReleaseDate = null,

        /** The status of the disbursement. */
        public readonly ?string $status = null,

        /** The status detail. */
        public readonly ?string $statusDetail = null,
    ) {}
}
