<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Sales Metric resource. Represents the seller's completed sales count for a specific
 * period, used as part of the overall seller reputation scoring.
 */
final class Sales implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The sales period (e.g., "365 days"). */
        public readonly ?string $period = null,

        /** The number of completed sales. */
        public readonly ?int $completed = null,
    ) {}
}
