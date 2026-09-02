<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents an external product category associated with an order item. Used to classify items
 * according to the seller's own category taxonomy, which can influence fraud analysis and payment
 * processing rules.
 */
final class ExternalCategory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Seller-defined category identifier for the item. */
        public readonly ?string $id = null,
    ) {}
}
