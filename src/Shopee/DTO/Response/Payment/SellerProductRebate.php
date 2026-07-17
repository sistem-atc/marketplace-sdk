<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Rebate de produto do vendedor (`order_income.seller_product_rebate`) — o
 * valor e quanto dele abate comissão/serviço.
 */
final class SellerProductRebate implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $amount = null,
        public readonly ?float $commissionFeeOffset = null,
        public readonly ?float $serviceFeeOffset = null,
    ) {}
}
