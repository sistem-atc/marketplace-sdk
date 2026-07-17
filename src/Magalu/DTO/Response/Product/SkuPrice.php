<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preço do SKU (`results[]` do priceBySku).
 *
 * INT NORMALIZADO (mesmo padrão do Order\Money): `price` (PARA) e `listPrice`
 * (DE) são inteiros; divida por `normalizer` (ex.: 5990/100 = R$ 59,90). Use
 * `price()` / `listPriceValue()`.
 */
final class SkuPrice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $price = null,
        public readonly ?int $listPrice = null,
        public readonly ?int $normalizer = null,
        public readonly ?string $currency = null,
        public readonly ?Channel $channel = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    /** Preço PARA em reais (÷ normalizer). */
    public function priceValue(): ?float
    {
        return $this->price === null ? null : $this->price / ($this->normalizer ?: 1);
    }

    /** Preço DE em reais (÷ normalizer). */
    public function listPriceValue(): ?float
    {
        return $this->listPrice === null ? null : $this->listPrice / ($this->normalizer ?: 1);
    }
}
