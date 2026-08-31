<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do de/para Tokopedia, compartilhada por:
 *   GET  /affiliate_creator/202606/toko_product_mappers
 *   POST /affiliate_creator/202607/map_toko_product  (V2)
 *
 * A chave da lista e `product` no SINGULAR, mesmo sendo array.
 *
 * @property list<TokoProductMapping>|null $product
 */
final class TokoProductMapperResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?TokoMapperError $error = null,
        #[ArrayOf(TokoProductMapping::class)]
        public readonly ?array $product = null,
    ) {}
}
