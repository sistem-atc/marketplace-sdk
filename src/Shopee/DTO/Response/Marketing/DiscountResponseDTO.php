<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/discount/get_discount` — o desconto com os itens
 * participantes. `itemList` é PAGINADO por `page_no` 1-based: pagine
 * enquanto `more` for true (os demais campos repetem a cada página).
 *
 * @property list<DiscountItem>|null $itemList
 */
final class DiscountResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $discountId = null,
        public readonly ?string $discountName = null,
        public readonly ?string $status = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $source = null,
        public readonly ?bool $more = null,
        #[ArrayOf(DiscountItem::class)]
        public readonly ?array $itemList = null,
    ) {}
}
