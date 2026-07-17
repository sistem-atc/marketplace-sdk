<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Authorization;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Loja autorizada (data.shops[]) de /authorization/202309/shops. O `cipher` é o
 * shop_cipher exigido em toda chamada de API TikTok — não vem no token nem no
 * get, só aqui. `id` é o shop_id (snowflake STRING).
 */
final class AuthorizedShop implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $region = null,
        public readonly ?string $sellerType = null,
        public readonly ?string $cipher = null,
        public readonly ?string $code = null,
    ) {}
}
