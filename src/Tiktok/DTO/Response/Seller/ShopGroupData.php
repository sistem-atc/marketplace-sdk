<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Seller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.shop_group_data` do Get Shop Groups: um único grupo (não é lista) mais
 * as lojas dele. Seller sem grupo de interoperabilidade recebe o objeto vazio.
 *
 * @property list<ShopGroupShop>|null $shops
 */
final class ShopGroupData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShopGroup $shopGroup = null,
        #[ArrayOf(ShopGroupShop::class)]
        public readonly ?array $shops = null,
    ) {}
}
