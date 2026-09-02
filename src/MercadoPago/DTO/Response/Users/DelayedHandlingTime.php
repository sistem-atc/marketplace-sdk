<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Delayed Handling Time Metric resource. Represents the seller's delayed handling
 * time metrics for a specific period, measuring how often the seller takes longer than expected
 * to ship orders.
 */
final class DelayedHandlingTime implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The delayed handling time period (e.g., "365 days"). */
        public readonly ?string $period = null,

        /** The delayed handling time rate (percentage). */
        public readonly ?float $rate = null,

        /** The delayed handling time value. */
        public readonly ?float $value = null,
    ) {}
}
