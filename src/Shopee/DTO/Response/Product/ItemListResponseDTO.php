<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/product/get_item_list` — paginação por OFFSET
 * (diferente do cursor usado em pedidos): pagine enquanto `hasNextPage`,
 * usando `nextOffset`.
 *
 * @property list<ItemSummary>|null $item
 */
final class ItemListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ItemSummary::class)]
        public readonly ?array $item = null,
        public readonly ?bool $hasNextPage = null,
        public readonly ?int $nextOffset = null,
        public readonly ?int $totalCount = null,
        public readonly ?string $next = null,
    ) {}
}
