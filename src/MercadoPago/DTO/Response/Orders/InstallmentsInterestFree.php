<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents interest-free installment promotion rules for a payment method. Defines promotional
 * installment plans where the seller absorbs the interest cost, allowing buyers to pay in
 * installments without additional charges.
 */
final class InstallmentsInterestFree implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Promotion type that determines who absorbs the interest cost (e.g., "seller_costs"). */
        public readonly ?string $type = null,

        /** Allowed installment counts for this promotion (e.g., [3, 6, 12]). */
        public readonly ?array $values = null,
    ) {}
}
