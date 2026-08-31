<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * No' da arvore de categorias globais.
 *
 * `permissionStatuses` diz se a LOJA pode usar a categoria: AVAILABLE ou
 * NON_MAIN_CATEGORY (fora do escopo do seller). So' categoria folha
 * (`isLeaf`) aceita produto.
 *
 * @property list<string>|null $permissionStatuses
 */
final class GlobalCategory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        // Raiz da arvore vem com "0".
        public readonly ?string $parentId = null,
        public readonly ?string $localName = null,
        public readonly ?bool $isLeaf = null,
        public readonly ?array $permissionStatuses = null,
    ) {}
}
