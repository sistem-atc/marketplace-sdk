<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Loja dona do produto. O affiliate_creator devolve SO o nome — nao ha
 * shop_id aqui (pra isso use o `shop_id` do fulfillment de amostra).
 */
final class ShopInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
    ) {}
}
