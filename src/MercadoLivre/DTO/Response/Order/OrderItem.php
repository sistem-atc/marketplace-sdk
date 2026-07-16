<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha do pedido (`order_items[]`) — produto + preço + quantidade + fee.
 *
 * @property array<int|string, mixed>|null $discounts
 */
final class OrderItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?OrderItemProduct $item = null,
        public readonly ?int $quantity = null,
        // requestedQuantity/stock vêm como OBJETO ({measure,unit}/alocação) em
        // parte dos payloads — mixed preserva escalar-ou-objeto sem perda.
        public readonly mixed $requestedQuantity = null,
        public readonly ?int $pickedQuantity = null,
        public readonly ?float $unitPrice = null,
        public readonly ?float $fullUnitPrice = null,
        public readonly ?float $grossPrice = null,
        public readonly ?float $saleFee = null,
        public readonly ?string $currencyId = null,
        public readonly ?string $baseCurrencyId = null,
        public readonly ?float $baseExchangeRate = null,
        public readonly ?string $listingTypeId = null,
        public readonly ?int $manufacturingDays = null,
        public readonly ?string $compatId = null,
        public readonly ?string $elementId = null,
        public readonly ?string $kitInstanceId = null,
        public readonly mixed $stock = null,
        public readonly ?bool $bundle = null,
        public readonly ?array $discounts = null,
    ) {}
}
