<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Advanced Payment resource. Represents a marketplace split payment, where a single collection is
 * distributed among multiple sellers (disbursements). Supports two-step flows (authorise →
 * capture) and individual disbursement release-date control.
 */
final class AdvancedPaymentResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The advanced payment ID. */
        public readonly ?int $id = null,

        /** The application ID. */
        public readonly ?string $applicationId = null,

        /** The external reference. */
        public readonly ?string $externalReference = null,

        /** The description. */
        public readonly ?string $description = null,

        /** The overall status of the advanced payment. */
        public readonly ?string $status = null,

        /** Indicates whether the payment was captured. */
        public readonly ?bool $capture = null,

        /** The binary mode flag. */
        public readonly ?bool $binaryMode = null,

        /** The date the advanced payment was created. */
        public readonly ?string $dateCreated = null,

        /** The date the advanced payment was last updated. */
        public readonly ?string $dateLastUpdated = null,

        /** The disbursements associated with this advanced payment. @var list<Disbursement>|null */
        #[ArrayOf(Disbursement::class)]
        public readonly ?array $disbursements = null,
    ) {}
}
