<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `POST /affiliate_seller/202608/target_collaborations/creator_promotion_details/query`.
 *
 * QUIRK: o payload util vem em `data.data` (envelope duplicado), diferente do
 * resto do grupo. O DTO preserva o aninhamento pra nao inventar shape.
 */
final class CreatorPromotionDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?CreatorPromotionDetailData $data = null,
    ) {}
}
