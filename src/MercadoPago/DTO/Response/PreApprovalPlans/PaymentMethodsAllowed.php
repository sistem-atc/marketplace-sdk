<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovalPlans;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * PreApproval Plan Allowed Payment Methods resource. Specifies which payment types and specific
 * payment methods are permitted for subscriptions created under this plan.
 */
final class PaymentMethodsAllowed implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment types. */
        public readonly ?array $paymentTypes = null,

        /** Payment methods. */
        public readonly ?array $paymentMethods = null,
    ) {}
}
