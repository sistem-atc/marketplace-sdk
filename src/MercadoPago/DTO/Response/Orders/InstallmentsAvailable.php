<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents general installment availability settings for a payment method. Defines which
 * installment options are available to the buyer when paying for an order.
 */
final class InstallmentsAvailable implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Installment availability type (e.g., "buyer_costs" when the buyer absorbs interest). */
        public readonly ?string $type = null,
    ) {}
}
