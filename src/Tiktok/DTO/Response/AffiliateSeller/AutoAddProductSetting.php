<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de entrada automatica de produtos na colaboracao aberta.
 *
 * Quando `enable=false` o TikTok NAO devolve `commissionRate` — e desligar nao
 * reverte as colaboracoes que ja existem.
 */
final class AutoAddProductSetting implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $enable = null,
        // centesimos de %: 1000 = 10,00%; faixa [100, 8000]
        public readonly ?int $commissionRate = null,
    ) {}
}
