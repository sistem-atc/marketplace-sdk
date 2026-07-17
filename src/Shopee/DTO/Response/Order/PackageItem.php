<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item dentro de um pacote (`package_list[].item_list[]`).
 *
 * Não confundir com OrderItem (`item_list[]` do pedido): aqui é só a
 * referência do que foi embalado — sem preço/nome. E `productLocationId` é
 * STRING aqui, contra LISTA no OrderItem (assimetria da própria Shopee).
 */
final class PackageItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $itemId = null,
        public readonly ?int $modelId = null,
        public readonly ?int $modelQuantity = null,
        public readonly ?int $orderItemId = null,
        public readonly ?int $promotionGroupId = null,
        public readonly ?string $productLocationId = null,
    ) {}
}
