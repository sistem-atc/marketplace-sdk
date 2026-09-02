<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Transactions resource. Provides a summary of the seller's transaction history for a
 * given period, including counts of cancelled, completed, and total transactions, along with
 * buyer rating distribution (positive, neutral, negative).
 */
final class Transactions implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The number of canceled transactions. */
        public readonly ?int $canceled = null,

        /** The number of completed transactions. */
        public readonly ?int $completed = null,

        /** The transaction period (e.g., "historic"). */
        public readonly ?string $period = null,

        /** User ratings and feedback statistics. */
        public readonly ?Ratings $ratings = null,

        /** The total number of transactions. */
        public readonly ?int $total = null,
    ) {}
}
