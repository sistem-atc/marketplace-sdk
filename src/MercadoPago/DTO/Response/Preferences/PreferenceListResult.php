<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference List Result resource. Represents a single preference summary within a search result
 * set. Contains a subset of the full Preference fields focused on identification, status, dates,
 * and configuration metadata.
 */
final class PreferenceListResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Preference ID. */
        public readonly ?string $id = null,

        /** List of items to be paid. */
        public readonly ?array $items = null,

        /** Client ID. */
        public readonly ?string $clientId = null,

        /** Reference you can synchronize with your payment system. */
        public readonly ?string $externalReference = null,

        /** True if a preference expires, false if not. */
        public readonly ?bool $expires = null,

        /** Date when the preference will be active. */
        public readonly ?string $expirationDateFrom = null,

        /** Date when the preference will be expired. */
        public readonly ?string $expirationDateTo = null,

        /** Collector ID. */
        public readonly ?int $collectorId = null,

        /** Origin of the payment. Default value: NONE. */
        public readonly ?string $marketplace = null,

        /** Operation type. */
        public readonly ?string $operationType = null,

        /** Configures which processing modes to use. */
        public readonly ?array $processingModes = null,

        /** Date of creation. */
        public readonly ?string $dateCreated = null,

        /** Site ID. */
        public readonly ?string $siteId = null,

        /** Product ID. */
        public readonly ?string $productId = null,

        /** Live mode. */
        public readonly ?bool $liveMode = null,

        /** Last modified. */
        public readonly ?string $lastUpdated = null,

        /** Purpose. */
        public readonly ?string $purpose = null,

        /** Total amount. */
        public readonly ?int $totalAmount = null,

        /** Shipping mode. */
        public readonly mixed $shippingMode = null,

        /** Sponsor ID. */
        public readonly ?int $sponsorId = null,

        /** Platform ID. */
        public readonly ?string $platformId = null,

        /** Payer ID. */
        public readonly ?int $payerId = null,

        /** Payer Email. */
        public readonly ?string $payerEmail = null,

        /** Integrator ID. */
        public readonly ?string $integratorId = null,

        /** Corporation ID. */
        public readonly ?string $corporationId = null,

        /** Concept ID. */
        public readonly ?string $conceptId = null,
    ) {}
}
