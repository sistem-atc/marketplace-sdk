<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a single identification type entry within the identification types list response.
 * Each entry describes a government-issued document category accepted for payment processing in a
 * given country (e.g., CPF in Brazil, DNI in Argentina, CURP in Mexico). Includes validation
 * constraints (min/max length) for the document number.
 */
final class IdentificationTypeResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identification type code (e.g., "CPF", "DNI", "CURP"). */
        public readonly ?string $id = null,

        /** Human-readable name of the identification type (e.g., "CPF", "DNI", "CURP"). */
        public readonly ?string $name = null,

        /** Category of identification document (e.g., "number"). */
        public readonly ?string $type = null,

        /** Minimum number of characters allowed for this identification number. */
        public readonly ?int $minLength = null,

        /** Maximum number of characters allowed for this identification number. */
        public readonly ?int $maxLength = null,
    ) {}
}
