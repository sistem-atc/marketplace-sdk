<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Autor do video na listagem. `authorType`: OFFICIAL | CHANNEL | AFFILIATE.
 */
final class ShopVideoCreator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $openId = null,
        public readonly ?string $userName = null,
        public readonly ?string $nickName = null,
        public readonly ?string $authorType = null,
    ) {}
}
