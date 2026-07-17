<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `discount_info` — desconto/bonificação aplicado sobre a cobrança.
 */
final class BillingDiscountInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $chargeAmountWithoutDiscount = null,
        public readonly ?float $discountAmount = null,
        public readonly ?string $discountReason = null,
        public readonly mixed $rebate = null,
    ) {}
}
