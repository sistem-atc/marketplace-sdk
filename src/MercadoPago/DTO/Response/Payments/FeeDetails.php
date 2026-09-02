<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents an individual fee applied to a payment in the MercadoPago API. Payments may have
 * multiple fee entries (e.g. MercadoPago commission, marketplace fee). Nested as an array within
 * the Payment response.
 */
final class FeeDetails implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Type of fee (e.g. "mercadopago_fee", "coupon_fee", "financing_fee"). */
        public readonly ?string $type = null,

        /** Party that absorbs the fee cost (e.g. "collector", "payer"). */
        public readonly ?string $feePayer = null,

        /** Fee amount in the payment's currency. */
        public readonly ?float $amount = null,
    ) {}
}
