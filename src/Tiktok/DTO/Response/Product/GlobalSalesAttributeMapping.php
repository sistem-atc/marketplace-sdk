<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * De/para de atributo de venda entre o produto GLOBAL e o LOCAL.
 * Os ids sao gerados por mercado — o mesmo "Vermelho" tem value_id
 * diferente em cada shop local.
 */
final class GlobalSalesAttributeMapping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $globalAttributeId = null,
        public readonly ?string $localAttributeId = null,
        public readonly ?string $globalValueId = null,
        public readonly ?string $localValueId = null,
    ) {}
}
