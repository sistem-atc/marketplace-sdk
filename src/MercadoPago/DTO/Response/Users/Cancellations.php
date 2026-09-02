<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Cancellations Metric resource. Represents the seller's order cancellation metrics
 * for a specific period, including the cancellation rate and absolute value used in reputation
 * scoring.
 */
final class Cancellations implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The cancellations period (e.g., "365 days"). */
        public readonly ?string $period = null,

        /** The cancellations rate (percentage). */
        public readonly ?float $rate = null,

        /** The cancellations value. */
        public readonly ?float $value = null,
    ) {}
}
