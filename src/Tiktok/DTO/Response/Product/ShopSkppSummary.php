<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resumo SKPP da loja.
 *
 * `earnedAdCredit` vem com o simbolo da moeda embutido ("$500") — por isso
 * STRING. `increasedVisibilityL30d` tambem e' string na doc, mesmo sendo contagem.
 */
final class ShopSkppSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $qualifiedProductsCount = null,
        public readonly ?int $eligibleProductsCount = null,
        public readonly ?string $earnedAdCredit = null,
        public readonly ?string $increasedVisibilityL30d = null,
    ) {}
}
