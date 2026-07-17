<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Item do pedido (`payload.OrderItems[]` de getOrderItems).
 *
 * `SellerSKU` é o de/para com o catálogo (via seller_sku do pedido = SKU
 * interno). Dinheiro é STRING (Money). `IsGift`/`NumberOfItems` são STRING.
 *
 * Siglas (`ASIN`, `SellerSKU`) usam #[JsonKey] — camelToPascal erraria.
 *
 * @property list<string>|null $promotionIds
 */
final class OrderItem implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[JsonKey('ASIN')]
        public readonly ?string $asin = null,
        #[JsonKey('SellerSKU')]
        public readonly ?string $sellerSku = null,
        public readonly ?string $orderItemId = null,
        public readonly ?string $title = null,
        public readonly ?int $quantityOrdered = null,
        public readonly ?int $quantityShipped = null,
        // Dinheiro (Money)
        public readonly ?Money $itemPrice = null,
        public readonly ?Money $itemTax = null,
        public readonly ?Money $promotionDiscount = null,
        public readonly ?Money $promotionDiscountTax = null,
        public readonly ?Money $shippingPrice = null,
        public readonly ?Money $shippingTax = null,
        public readonly ?Money $shippingDiscount = null,
        public readonly ?Money $shippingDiscountTax = null,
        // STRING na SP-API
        public readonly ?string $isGift = null,
        public readonly ?bool $isTransparency = null,
        public readonly ?string $conditionId = null,
        public readonly ?string $conditionSubtypeId = null,
        public readonly ?string $conditionNote = null,
        public readonly ?ProductInfo $productInfo = null,
        public readonly ?ItemBuyerInfo $buyerInfo = null,
        public readonly ?BuyerRequestedCancel $buyerRequestedCancel = null,
        public readonly ?AmazonPrograms $amazonPrograms = null,
        public readonly ?array $promotionIds = null,
    ) {}
}
