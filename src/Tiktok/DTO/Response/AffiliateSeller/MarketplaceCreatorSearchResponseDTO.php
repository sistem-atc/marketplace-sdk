<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `POST /affiliate_seller/202608/marketplace_creators/search`.
 *
 * `searchKey` NAO e' cosmetico: devolva-o no proximo request pra a paginacao
 * cair sobre o mesmo resultado cacheado. Sem ele a pagina 2 pode vir de um
 * rank recalculado e repetir/pular creators.
 */
final class MarketplaceCreatorSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        // reenvie no proximo request pra estabilizar a paginacao
        public readonly ?string $searchKey = null,
        #[ArrayOf(MarketplaceCreatorSummary::class)]
        public readonly ?array $creators = null,
    ) {}
}
