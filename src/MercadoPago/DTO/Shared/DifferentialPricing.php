<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a differential pricing configuration in the MercadoPago API. Differential pricing
 * allows sellers to offer different installment plans or financing conditions for specific
 * payment scenarios. The ID references a pre-configured pricing plan on the MercadoPago platform.
 */
final class DifferentialPricing implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the differential pricing configuration. */
        public readonly ?int $id = null,
    ) {}
}
