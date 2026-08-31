<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comissao de Shop Ads: so' vale pros pedidos gerados por anuncio.
 * `rate` em centesimos de por cento (ver ProductCommission).
 */
final class ShopAdsCommission implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $rate = null,
    ) {}
}
