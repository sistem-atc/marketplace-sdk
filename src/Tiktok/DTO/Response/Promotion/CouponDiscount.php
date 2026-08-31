<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Desconto do cupom. `type` AMOUNT_OFF usa `reductionAmount`; PERCENT_OFF usa
 * `percentage` (STRING em PONTOS PERCENTUAIS — "30" é 30%, não 0,30) e o teto
 * opcional `maxDiscount`. A API preenche os campos dos dois tipos no mesmo
 * objeto; quem decide é o `type`.
 */
final class CouponDiscount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?CouponMoney $reductionAmount = null,
        public readonly ?string $percentage = null,
        public readonly ?CouponMoney $maxDiscount = null,
    ) {}
}
