<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha da entrega (`deliveries[].items[]`). `unitPrice` é Money (usa `value`);
 * `amounts` são os totais da linha. `taxes` tem shape livre.
 *
 * @property array<int, mixed>|null $taxes
 */
final class DeliveryItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ItemInfo $info = null,
        public readonly ?int $quantity = null,
        public readonly ?string $measureUnit = null,
        public readonly ?int $sequencial = null,
        public readonly ?Money $unitPrice = null,
        public readonly ?Amounts $amounts = null,
        public readonly ?array $taxes = null,
    ) {}
}
