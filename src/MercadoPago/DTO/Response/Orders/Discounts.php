<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents discount rules applied to a MercadoPago order. Discounts are organized by payment
 * method, allowing different discount amounts depending on how the buyer pays.
 */
final class Discounts implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Discount definitions per payment method type. Each element maps to PaymentMethodDiscount. @var list<PaymentMethodDiscount>|null */
        #[ArrayOf(PaymentMethodDiscount::class)]
        public readonly ?array $paymentMethods = null,
    ) {}
}
