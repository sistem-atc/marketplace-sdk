<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/seller/v1/portfolios/prices/{sku}` — envelope `results`
 * (normalmente 1 item, o preço do SKU) + `meta`.
 *
 * @property list<SkuPrice>|null $results
 */
final class SkuPriceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SkuPrice::class)]
        public readonly ?array $results = null,
        public readonly ?Meta $meta = null,
    ) {}

    /** Atalho: o preço do SKU (1º result). */
    public function first(): ?SkuPrice
    {
        return $this->results[0] ?? null;
    }
}
