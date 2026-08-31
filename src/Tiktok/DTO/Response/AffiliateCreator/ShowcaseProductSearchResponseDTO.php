<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de GET /affiliate_creator/202405/showcases/products.
 *
 * @property list<ShowcaseProduct>|null $products
 */
final class ShowcaseProductSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ShowcaseProduct::class)]
        public readonly ?array $products = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
