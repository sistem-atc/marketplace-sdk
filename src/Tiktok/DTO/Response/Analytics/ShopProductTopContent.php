<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo (live ou video) que vendeu o produto. `contentId` = LIVE session id OU video id.
 */
final class ShopProductTopContent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $contentId = null,
        public readonly ?int $itemsSold = null,
        public readonly ?MonetaryValue $gmv = null,
    ) {}
}
