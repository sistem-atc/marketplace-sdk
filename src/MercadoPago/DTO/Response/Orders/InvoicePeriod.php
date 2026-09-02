<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the billing period for a subscription invoice within an order. Defines the frequency
 * and type of billing cycle used in recurring payment scenarios.
 */
final class InvoicePeriod implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Number of units in the billing cycle (e.g., 1 for monthly, 7 for weekly). */
        public readonly ?int $period = null,

        /** Unit type of the billing period (e.g., "monthly", "daily"). */
        public readonly ?string $type = null,
    ) {}
}
