<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Payment Methods configuration resource. Configures which payment methods are
 * available for a checkout preference. Allows setting a default payment method, installment
 * limits, excluded payment methods, excluded payment types, and a default card. Fields are mapped
 * to nested DTOs: - excluded_payment_methods -> PaymentMethod - excluded_payment_types ->
 * PaymentType
 */
final class PaymentMethods implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Default payment method ID to pre-select in checkout. */
        public readonly ?string $defaultPaymentMethodId = null,

        /** Maximum number of installments allowed. */
        public readonly ?int $installments = null,

        /** Default number of installments pre-selected in checkout. */
        public readonly ?int $defaultInstallments = null,

        /** Payment methods not allowed in payment process (except account_money). @var list<PaymentMethod>|null */
        #[ArrayOf(PaymentMethod::class)]
        public readonly ?array $excludedPaymentMethods = null,

        /** Payment types not allowed in payment process. @var list<PaymentType>|null */
        #[ArrayOf(PaymentType::class)]
        public readonly ?array $excludedPaymentTypes = null,

        /** Default card ID. */
        public readonly ?string $defaultCardId = null,
    ) {}
}
