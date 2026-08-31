<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU global devolvido por create/edit — o eco dos ids gerados.
 *
 * Nao traz preco nem estoque: e' so' o de/para entre o `sellerSku` que voce
 * mandou e o `id` que o TikTok criou.
 *
 * @property list<GlobalCreatedSalesAttribute>|null $salesAttributes
 */
final class GlobalCreatedSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $sellerSku = null,
        public readonly ?string $externalGlobalSkuId = null,
        #[ArrayOf(GlobalCreatedSalesAttribute::class)]
        public readonly ?array $salesAttributes = null,
    ) {}
}
