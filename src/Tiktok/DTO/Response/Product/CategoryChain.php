<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Nível da árvore de categorias (`category_chains[]`) — a categoria completa
 * é a cadeia do raiz até `isLeaf: true`.
 */
final class CategoryChain implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $parentId = null,
        public readonly ?string $localName = null,
        public readonly ?bool $isLeaf = null,
    ) {}
}
