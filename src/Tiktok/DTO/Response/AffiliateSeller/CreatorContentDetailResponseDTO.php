<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `GET /affiliate_seller/202508/open_collaborations/creator_content_details`.
 *
 * O `product` fica FORA da lista: e' o produto do filtro, nao um por item.
 */
final class CreatorContentDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
        #[ArrayOf(CreatorContentDetail::class)]
        public readonly ?array $creatorContentDetails = null,
        public readonly ?ContentDetailProduct $product = null,
    ) {}
}
