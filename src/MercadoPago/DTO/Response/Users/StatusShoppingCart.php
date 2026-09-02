<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Status Shopping Cart resource. Represents the shopping cart permissions for a user
 * account, indicating whether buying and selling through the cart are allowed.
 */
final class StatusShoppingCart implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indicates whether buying from the shopping cart is allowed. */
        public readonly ?string $buy = null,

        /** Indicates whether selling from the shopping cart is allowed. */
        public readonly ?string $sell = null,
    ) {}
}
