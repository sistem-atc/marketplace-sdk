<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Categoria sugerida pelo recomendador. Shape DIFERENTE de GlobalCategory:
 * aqui vem `name` + `level` (profundidade na arvore), sem parent_id.
 */
final class GlobalRecommendedCategory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?int $level = null,
        public readonly ?bool $isLeaf = null,
    ) {}
}
