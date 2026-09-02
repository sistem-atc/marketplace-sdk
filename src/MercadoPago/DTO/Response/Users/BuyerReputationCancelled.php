<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Buyer Reputation Cancelled Transactions resource. Tracks the number of cancelled
 * transactions in a buyer's reputation, distinguishing between paid-then-cancelled and total
 * cancelled transactions.
 */
final class BuyerReputationCancelled implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The number of paid canceled transactions. */
        public readonly mixed $paid = null,

        /** The total number of canceled transactions. */
        public readonly mixed $total = null,
    ) {}
}
