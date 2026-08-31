<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202509/open_collaborations/products —
 * busca DIRETA por product_ids (sem paginacao nem total_count).
 *
 * @property list<OpenCollaborationProduct>|null $products
 */
final class OpenCollaborationProductListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(OpenCollaborationProduct::class)]
        public readonly ?array $products = null,
    ) {}
}
