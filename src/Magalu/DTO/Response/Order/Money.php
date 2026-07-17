<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Valor monetário do Magalu — INTEIRO NORMALIZADO, não decimal.
 *
 * O Magalu manda dinheiro como objeto {currency, normalizer, total/value}: o
 * valor cru é INT e `normalizer` é a escala (ex.: total=5990, normalizer=100
 * ⇒ R$ 59,90). NUNCA leia total/value como reais — divida por normalizer (use o helper `amount()`); sem normalizer, o valor
 * ja esta na unidade final (÷1).
 *
 * Duas shapes convivem: os agregados (`amounts.*`) usam `total` (+ `type`);
 * o preço unitário (`unit_price`) usa `value`. Ambos mapeados; um só é
 * preenchido por vez.
 */
final class Money implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?int $normalizer = null,
        public readonly ?int $total = null,
        public readonly ?int $value = null,
        public readonly ?string $type = null,
    ) {}

    /** Valor em reais (total|value ÷ normalizer). */
    public function amount(): ?float
    {
        $raw = $this->total ?? $this->value;
        if ($raw === null) {
            return null;
        }

        return $raw / ($this->normalizer ?: 1);
    }
}
