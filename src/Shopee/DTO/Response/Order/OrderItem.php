<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha do pedido (`item_list[]`) — produto/variação + preço + quantidade.
 *
 * O SKU interno do vendedor vem em `modelSku` (variação) ou `itemSku`
 * (anúncio-pai) — é o de/para com o catálogo do ERP.
 *
 * @property array<int, mixed>|null $productLocationId
 * @property list<ItemPromotion>|null $promotionList
 */
final class OrderItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $itemId = null,
        public readonly ?string $itemName = null,
        public readonly ?string $itemSku = null,
        public readonly ?int $modelId = null,
        public readonly ?string $modelName = null,
        public readonly ?string $modelSku = null,
        public readonly ?int $modelQuantityPurchased = null,
        public readonly ?float $modelOriginalPrice = null,
        public readonly ?float $modelDiscountedPrice = null,
        public readonly ?bool $wholesale = null,
        public readonly ?float $weight = null,
        public readonly ?bool $addOnDeal = null,
        public readonly ?bool $mainItem = null,
        public readonly ?int $addOnDealId = null,
        public readonly ?string $promotionType = null,
        public readonly ?int $promotionId = null,
        public readonly ?int $orderItemId = null,
        public readonly ?int $lineItemId = null,
        public readonly ?int $promotionGroupId = null,
        public readonly ?ItemImageInfo $imageInfo = null,
        // Em `item_list[]` vem como LISTA (difere do string em
        // `package_list[].item_list[]` — ver PackageItem).
        public readonly ?array $productLocationId = null,
        public readonly ?bool $isPrescriptionItem = null,
        public readonly ?bool $errorInFetchingIsPrescriptionItem = null,
        public readonly ?string $consultationId = null,
        public readonly ?bool $isB2cOwnedItem = null,
        public readonly ?bool $hotListingItem = null,
        public readonly ?int $activeQty = null,
        public readonly ?int $cancelledQty = null,
        public readonly ?int $cancelRequestedQty = null,
        public readonly ?int $returnedQty = null,
        public readonly ?int $returnRequestedQty = null,
        #[ArrayOf(ItemPromotion::class)]
        public readonly ?array $promotionList = null,
    ) {}
}
