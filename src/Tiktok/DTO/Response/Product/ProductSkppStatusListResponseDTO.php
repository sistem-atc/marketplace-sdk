<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202606/skpps/search`.
 *
 * Pagina por page_no/page_size (NAO por page_token, ao contrario do resto do
 * grupo Product). `updateTime` e' o snapshot offline — ver ProductSkppDetailResponseDTO.
 */
final class ProductSkppStatusListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SkppProductStatus::class)]
        public readonly ?array $products = null,
        public readonly ?int $totalCount = null,
        public readonly ?int $updateTime = null,
    ) {}
}
