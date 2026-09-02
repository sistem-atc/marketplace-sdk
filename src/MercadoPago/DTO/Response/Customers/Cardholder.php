<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;

/**
 * Represents the holder of a payment card. Contains the cardholder's name as printed on the card
 * and their personal identification document, used for fraud prevention and payment validation.
 */
final class Cardholder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Full name of the cardholder as printed on the card. */
        public readonly ?string $name = null,

        /** Cardholder's personal identification document (e.g., CPF, DNI). */
        public readonly ?Identification $identification = null,
    ) {}
}
