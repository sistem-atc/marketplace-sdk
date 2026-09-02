<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Ratings resource. Represents the seller's rating distribution from completed
 * transactions, broken down into negative, neutral, and positive feedback counts.
 */
final class Ratings implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The number of negative ratings. */
        public readonly ?int $negative = null,

        /** The number of neutral ratings. */
        public readonly ?int $neutral = null,

        /** The number of positive ratings. */
        public readonly ?int $positive = null,
    ) {}
}
