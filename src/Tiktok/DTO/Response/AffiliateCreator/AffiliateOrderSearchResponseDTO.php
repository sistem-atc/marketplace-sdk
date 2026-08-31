<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202410/orders/search.
 *
 * @property list<AffiliateOrder>|null $orders
 */
final class AffiliateOrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(AffiliateOrder::class)]
        public readonly ?array $orders = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
