<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;

/**
 * Represents the cardholder (owner of the payment card) in the MercadoPago API. Contains the
 * cardholder's name and identification document as printed on the card or registered with the
 * issuer. Nested within Card and CardToken.
 */
final class Cardholder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Full name of the cardholder as it appears on the card. */
        public readonly ?string $name = null,

        /** Cardholder's identification document (type and number). */
        public readonly ?Identification $identification = null,
    ) {}
}
