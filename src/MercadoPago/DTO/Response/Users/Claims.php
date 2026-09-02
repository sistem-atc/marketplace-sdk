<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Claims Metric resource. Represents the seller's claims/disputes metrics for a
 * specific period, including the claim rate and absolute value used in reputation scoring.
 */
final class Claims implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The claims period (e.g., "365 days"). */
        public readonly ?string $period = null,

        /** The claims rate (percentage). */
        public readonly ?float $rate = null,

        /** The claims value. */
        public readonly ?float $value = null,
    ) {}
}
