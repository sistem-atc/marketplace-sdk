<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagens do anúncio (`image` e `promotion_image`) — 2 listas PARALELAS
 * (id e url no mesmo índice), não uma lista de objetos.
 *
 * @property list<string>|null $imageIdList
 * @property list<string>|null $imageUrlList
 */
final class ItemImage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?array $imageIdList = null,
        public readonly ?array $imageUrlList = null,
        public readonly ?string $imageRatio = null,
    ) {}
}
