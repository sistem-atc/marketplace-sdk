<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item real por tras de um item de pedido virtual (bundle/unboxing), com o
 * rateio de valor pra alfandega.
 *
 * `pricePercent` e' INT em PONTOS PERCENTUAIS: 10 = 10%. Nao e' fracao.
 */
final class RelatedItemDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $skuId = null,
        public readonly ?int $qty = null,
        public readonly ?int $pricePercent = null,
    ) {}
}
