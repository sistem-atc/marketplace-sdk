<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ajuste no repasse (`order_adjustment[]`) — crédito/débito avulso que a
 * Shopee lança sobre o pedido depois do fato (ex.: DIFAL, compensação).
 *
 * `amount` pode ser NEGATIVO (débito). `date` é epoch em SEGUNDOS.
 */
final class OrderAdjustment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?int $date = null,
        public readonly ?string $adjustmentReason = null,
    ) {}
}
