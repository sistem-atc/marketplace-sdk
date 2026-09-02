<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Metrics resource. Aggregates the seller's key performance metrics used for
 * reputation scoring, including sales volume, claims rate, delayed handling time, and
 * cancellation rate. Fields are mapped to nested DTOs: - sales -> Sales - claims -> Claims -
 * delayed_handling_time -> DelayedHandlingTime - cancellations -> Cancellations
 */
final class Metrics implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** User sales metrics for a specific period. */
        public readonly ?Sales $sales = null,

        /** User claims metrics for a specific period. */
        public readonly ?Claims $claims = null,

        /** User delayed handling time metrics for a specific period. */
        public readonly ?DelayedHandlingTime $delayedHandlingTime = null,

        /** User cancellations metrics for a specific period. */
        public readonly ?Cancellations $cancellations = null,
    ) {}
}
