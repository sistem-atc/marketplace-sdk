<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Link de promoção gerado pra um produto no lote. */
final class ProductPromotionLink implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        /** Chave `link` no lote; no endpoint unitário chama `product_promotion_link`. */
        public readonly ?string $link = null,
    ) {}
}
