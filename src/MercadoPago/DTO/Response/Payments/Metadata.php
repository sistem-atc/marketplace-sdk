<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents custom metadata associated with a payment in the MercadoPago API. Allows integrators
 * to attach key-value data to payments for internal tracking and reconciliation purposes. Nested
 * within Payment.
 */
final class Metadata implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Integrator-defined order number for mapping payments to internal orders. */
        public readonly ?string $orderNumber = null,
    ) {}
}
