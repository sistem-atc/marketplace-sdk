<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha de comissão/serviço líquido (`net_commission_fee_info_list[]` e
 * `net_service_fee_info_list[]` — mesma shape, `category` só na de serviço).
 *
 * É o detalhamento por REGRA da taxa cobrada: `ruleId` identifica a regra na
 * Shopee e `feeAmount` é o valor dela nesse pedido.
 */
final class NetFeeInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $ruleId = null,
        public readonly ?string $ruleDisplayName = null,
        public readonly ?float $feeAmount = null,
        public readonly ?string $category = null,
    ) {}
}
