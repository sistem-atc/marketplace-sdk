<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Method Financial Institution resource. Represents a bank or financial institution
 * associated with a payment method, typically used for bank transfer payment types where the
 * buyer selects their bank.
 */
final class FinancialInstitutions implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the financial institution in the MercadoPago catalog. */
        public readonly ?int $id = null,

        /** Human-readable name of the financial institution (e.g., "Banco Nacion", "Bradesco"). */
        public readonly ?string $description = null,
    ) {}
}
