<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Buyer Reputation Unrated Transactions resource. Tracks transactions where the buyer did
 * not provide a rating, broken down by paid and total counts.
 */
final class BuyerReputationUnrated implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The number of paid unrated transactions. */
        public readonly mixed $paid = null,

        /** The total number of unrated transactions. */
        public readonly mixed $total = null,
    ) {}
}
