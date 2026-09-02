<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a tax entry applied to a MercadoPago order. Each tax defines its type, amount, and
 * the fiscal condition of the payer that determines tax applicability.
 */
final class Taxes implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Fiscal condition of the payer that determines tax applicability (e.g., "IVA_responsable_inscripto"). */
        public readonly ?string $payerCondition = null,

        /** Tax amount or rate applied to the order. */
        public readonly ?string $value = null,

        /** Tax type identifier (e.g., "IVA", "IGMP"). */
        public readonly ?string $type = null,
    ) {}
}
