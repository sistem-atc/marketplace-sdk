<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto do item (`order_items[].item`) — o anúncio (MLB), SKU, variação.
 *
 * `attributes`/`variationAttributes` ficam como array cru: o ML varia o shape
 * (lista de objetos {id,name,value_name} OU lista de strings), então tipar item
 * a item quebraria — array preserva qualquer forma sem perda.
 *
 * @property array<int|string, mixed> $attributes
 * @property array<int|string, mixed> $variationAttributes
 */
final class OrderItemProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>  $attributes
     * @param  list<VariationAttribute>  $variationAttributes
     */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $categoryId = null,
        public readonly ?string $variationId = null,
        public readonly ?string $sellerCustomField = null,
        public readonly ?string $sellerSku = null,
        public readonly ?bool $globalPrice = null,
        public readonly ?float $netWeight = null,
        public readonly ?string $userProductId = null,
        public readonly ?string $releaseDate = null,
        public readonly ?string $warranty = null,
        public readonly ?string $condition = null,
        public readonly array $attributes = [],
        public readonly array $variationAttributes = [],
    ) {}
}
