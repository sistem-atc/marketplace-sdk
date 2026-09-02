<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the order associated with a payment in the MercadoPago API. Links a payment to an
 * order entity, which can represent a MercadoPago marketplace order or a merchant order. Nested
 * within Payment.
 */
final class Order implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the associated order. */
        public readonly ?int $id = null,

        /** Order type (e.g. "mercadolibre", "mercadopago"). */
        public readonly ?string $type = null,
    ) {}
}
