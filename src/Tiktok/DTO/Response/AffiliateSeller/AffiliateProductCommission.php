<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comissao do produto na vitrine: taxa em centesimos de por cento (`rate`) E
 * o valor em dinheiro como STRING (`amount`).
 */
final class AffiliateProductCommission implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // centesimos de %: 3000 = 30,00%
        public readonly ?int $rate = null,
        public readonly ?string $currency = null,
        public readonly ?string $amount = null,
    ) {}
}
