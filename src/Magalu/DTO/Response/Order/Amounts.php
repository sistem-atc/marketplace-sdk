<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Totais do pedido/entrega (`amounts`). `total` é INT normalizado (ver Money);
 * os componentes (comissão, desconto, frete, imposto) são Money aninhados.
 */
final class Amounts implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?int $normalizer = null,
        public readonly ?int $total = null,
        public readonly ?Money $commission = null,
        public readonly ?Money $discount = null,
        public readonly ?Money $freight = null,
        public readonly ?Money $tax = null,
    ) {}

    /** `total` em reais (÷ normalizer). */
    public function amount(): ?float
    {
        return $this->total === null ? null : $this->total / ($this->normalizer ?: 100);
    }
}
