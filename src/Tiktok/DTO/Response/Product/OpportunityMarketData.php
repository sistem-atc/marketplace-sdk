<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Sinais de mercado dos ultimos 30 dias. Valores tambem sao STRING formatada
 * ("100k+", "$9M+") — e o GMV vem sempre em USD, nao na moeda da loja.
 */
final class OpportunityMarketData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $estMarketSalesVolume = null,
        public readonly ?string $estMarketGmv = null,
        public readonly ?string $marketBuzz = null,
        public readonly ?string $searchVolume = null,
        public readonly ?string $searchKeyword = null,
        public readonly ?string $competitorProductCount = null,
        public readonly ?string $competitorSellerCount = null,
    ) {}
}
