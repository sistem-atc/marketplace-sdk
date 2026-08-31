<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Loja elegível a se inscrever na campanha. */
final class TargetShop implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** O shop code do Seller Center, não o shop_id da API. */
        public readonly ?string $code = null,
        public readonly ?string $name = null,
    ) {}
}
