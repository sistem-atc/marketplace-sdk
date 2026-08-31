<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Seller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Loja participante do grupo. IDs são STRING (snowflake). Aqui NÃO vem
 * shop_cipher — pra chamar as APIs de negócio dessa loja ainda é preciso o
 * cipher do Get Authorized Shops.
 */
final class ShopGroupShop implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $shopId = null,
        public readonly ?string $sellerId = null,
        public readonly ?string $shopName = null,
        public readonly ?string $shopRegion = null,
    ) {}
}
