<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the payment method used for a payment in the MercadoPago API. Provides details about
 * which payment method was selected and any associated method-specific data such as references
 * and rules. Nested within Payment.
 */
final class PaymentMethod implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Method-specific data including references and rules. @var list<PaymentMethodData>|null */
        #[ArrayOf(PaymentMethodData::class)]
        public readonly ?array $data = null,

        /** Payment method identifier (e.g. "visa", "pix", "bolbradesco"). */
        public readonly ?string $id = null,

        /** Payment method type (e.g. "credit_card", "debit_card", "ticket", "bank_transfer"). */
        public readonly ?string $type = null,

        /** Identifier of the card issuer or financial institution. */
        public readonly ?string $issuerId = null,
    ) {}
}
