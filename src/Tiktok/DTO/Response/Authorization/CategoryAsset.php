<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Authorization;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.category_assets[]` de /authorization/202405/category_assets.
 *
 * É o análogo de PARCEIRO do AuthorizedShop: o `cipher` aqui identifica o
 * PARCEIRO (prefixo TTP_) na chamada, não uma loja — não confundir com o
 * shop_cipher. Exige access_token de parceiro (user_type=3), não de seller.
 */
final class CategoryAsset implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cipher = null,
        public readonly ?string $targetMarket = null,
        public readonly ?BusinessCategory $category = null,
    ) {}
}
