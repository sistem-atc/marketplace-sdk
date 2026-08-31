<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `POST /affiliate_seller/202608/marketplace_creators/search/filter`.
 *
 * Os filtros MUDAM por pais e por conta — descubra aqui antes de montar o
 * body da busca, em vez de chutar constante. Envelope duplicado (`data.data`).
 */
final class CreatorMarketplaceFiltersResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?CreatorFilterData $data = null,
    ) {}
}
