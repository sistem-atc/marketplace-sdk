<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/product/get_item_base_info`.
 *
 * `warning` traz aviso não-fatal da Shopee (ex.: item_id inexistente no
 * lote) — a chamada volta 200 mesmo assim, com o item faltando da lista.
 *
 * @property list<ItemResponseDTO>|null $itemList
 */
final class ItemBaseInfoResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ItemResponseDTO::class)]
        public readonly ?array $itemList = null,
        public readonly ?string $warning = null,
    ) {}
}
