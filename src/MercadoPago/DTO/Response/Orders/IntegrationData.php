<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents integration metadata for a MercadoPago order. Contains identifiers that link the
 * order to a specific platform, integrator, application, or corporation within the MercadoPago
 * partner ecosystem.
 */
final class IntegrationData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identifier of the corporation that owns the integration. */
        public readonly ?string $corporationId = null,

        /** MercadoPago application ID used to create the order. */
        public readonly ?string $applicationId = null,

        /** Certified integrator identifier assigned by MercadoPago. */
        public readonly ?string $integratorId = null,

        /** E-commerce platform identifier (e.g., for WooCommerce, Magento, etc.). */
        public readonly ?string $platformId = null,

        /** Sponsor details when the order is created on behalf of another account. */
        public readonly ?Sponsor $sponsor = null,
    ) {}
}
