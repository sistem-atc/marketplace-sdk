<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `global_products/search` — paginacao por PAGE TOKEN opaco.
 * Pagine enquanto `nextPageToken` vier preenchido.
 *
 * A lista vem em `global_products` (nao `products`, como no search local).
 * `totalCount` estoura em 10000: a API recusa passar disso.
 *
 * @property list<GlobalProductResponseDTO>|null $globalProducts
 */
final class GlobalProductSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalProductResponseDTO::class)]
        public readonly ?array $globalProducts = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
