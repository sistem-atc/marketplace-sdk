<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Method List Result resource. Represents a single payment method entry returned when
 * listing available payment methods for a country/site. Contains the method's type, status,
 * amount limits, accreditation time, card settings, and associated financial institutions.
 */
final class PaymentMethodResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment method ID. */
        public readonly ?string $id = null,

        /** Payment method name. */
        public readonly ?string $name = null,

        /** Payment method payment type ID. */
        public readonly ?string $paymentTypeId = null,

        /** Payment method status. */
        public readonly ?string $status = null,

        /** Payment method secure thumbnail. */
        public readonly ?string $secureThumbnail = null,

        /** Payment method thumbnail. */
        public readonly ?string $thumbnail = null,

        /** Payment method deferred capture. */
        public readonly ?string $deferredCapture = null,

        /** Payment method settings. @var list<Settings>|null */
        #[ArrayOf(Settings::class)]
        public readonly ?array $settings = null,

        /** Payment method min allowed amount. */
        public readonly ?float $minAllowedAmount = null,

        /** Payment method max allowed amount. */
        public readonly ?float $maxAllowedAmount = null,

        /** Payment method accreditation time. */
        public readonly ?int $accreditationTime = null,

        /** Payment method financial institutions. */
        public readonly ?FinancialInstitutions $financialInstitutions = null,

        /** Payment method processing modes. */
        public readonly ?array $processingModes = null,

        /** Payment method additional info needed. */
        public readonly mixed $additionalInfoNeeded = null,
    ) {}
}
