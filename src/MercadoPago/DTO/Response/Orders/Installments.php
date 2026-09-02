<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents installment plan configuration for an order's payment method. Groups both
 * interest-free promotions and general installment availability settings that determine how
 * payments can be split.
 */
final class Installments implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Interest-free installment promotion rules. */
        public readonly ?InstallmentsInterestFree $interestFree = null,

        /** General installment availability settings. */
        public readonly ?InstallmentsAvailable $available = null,
    ) {}
}
