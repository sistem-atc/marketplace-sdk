<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the financial institution (bank or card network) that issued a payment card.
 * Contains the issuer's MercadoPago identifier and display name. Used as a nested object within
 * CustomerCard and CustomerCardListResult.
 */
final class Issuer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** MercadoPago identifier for this card issuer. */
        public readonly ?int $id = null,

        /** Display name of the card issuer (e.g., "Banco Nacion", "BBVA"). */
        public readonly ?string $name = null,
    ) {}
}
