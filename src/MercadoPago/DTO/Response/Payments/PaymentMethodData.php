<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents method-specific data within a payment method in the MercadoPago API. Contains
 * references and business rules (discounts, fines, interest) that apply to the selected payment
 * method. Nested within PaymentMethod.
 */
final class PaymentMethodData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Business rules applied to the payment method (discounts, fine, interest). */
        public readonly ?PaymentMethodRules $rules = null,

        /** Internal reference identifier for the payment method transaction. */
        public readonly ?string $referenceId = null,

        /** External reference identifier for cross-system reconciliation. */
        public readonly ?string $externalReferenceId = null,

        /** URL to an external resource related to the payment (e.g. boleto PDF, ticket page). */
        public readonly ?string $externalResourceUrl = null,
    ) {}
}
