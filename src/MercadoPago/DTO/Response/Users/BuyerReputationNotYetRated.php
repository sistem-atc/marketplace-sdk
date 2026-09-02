<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Buyer Reputation Not Yet Rated Transactions resource. Tracks transactions that the buyer
 * has completed but has not yet provided a rating for, broken down by paid count, total count,
 * and units.
 */
final class BuyerReputationNotYetRated implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The number of paid transactions not yet rated. */
        public readonly mixed $paid = null,

        /** The total number of transactions not yet rated. */
        public readonly mixed $total = null,

        /** The number of units not yet rated. */
        public readonly mixed $units = null,
    ) {}
}
