<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha do pedido no escrow (`order_income.items[]`) — a visão FINANCEIRA do
 * item (o que foi cobrado/descontado), diferente da linha fiscal/logística do
 * `Order\OrderItem`.
 *
 * Todo campo de dinheiro é `?float` mesmo quando a Shopee manda inteiro: tipar
 * int truncaria centavos em silêncio quando o valor vier decimal.
 *
 * @property list<EscrowItemPromotion>|null $promotionList
 * @property array<int, mixed>|null $kitItems
 */
final class EscrowItem implements DTOInterface
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
        public readonly ?int $lineItemId = null,
        public readonly ?int $quantityPurchased = null,
        // Preços
        public readonly ?float $originalPrice = null,
        public readonly ?float $discountedPrice = null,
        public readonly ?float $sellingPrice = null,
        // Descontos
        public readonly ?float $sellerDiscount = null,
        public readonly ?float $discountFromCoin = null,
        public readonly ?float $discountFromVoucherSeller = null,
        public readonly ?float $discountFromVoucherShopee = null,
        // Taxas
        public readonly ?float $amsCommissionFee = null,
        public readonly ?float $sellerOrderProcessingFee = null,
        public readonly ?float $installationFeePaidByBuyer = null,
        public readonly ?float $buyerPaidExtendedWarranty = null,
        public readonly ?float $bcrsDeposit = null,
        // Campanha
        public readonly ?int $activityId = null,
        public readonly ?string $activityType = null,
        // Flags
        public readonly ?bool $isMainItem = null,
        public readonly ?bool $isB2cShopItem = null,
        public readonly ?bool $isKit = null,
        #[ArrayOf(EscrowItemPromotion::class)]
        public readonly ?array $promotionList = null,
        // Sempre vazio no corpus BR; a Shopee não documenta a shape interna.
        public readonly ?array $kitItems = null,
    ) {}
}
