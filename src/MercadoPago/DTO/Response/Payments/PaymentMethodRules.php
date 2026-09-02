<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents business rules (discounts, fines, interest) applied to a payment method. Configures
 * early-payment discounts, late-payment fines, and interest charges for offline payment methods
 * like boleto. Nested within PaymentMethodData.
 */
final class PaymentMethodRules implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Early-payment discount rules. @var list<PaymentDiscounts>|null */
        #[ArrayOf(PaymentDiscounts::class)]
        public readonly ?array $discounts = null,

        /** Late-payment fine configuration. */
        public readonly ?PaymentFee $fine = null,

        /** Interest charge configuration for overdue payments. */
        public readonly ?PaymentFee $interest = null,
    ) {}
}
