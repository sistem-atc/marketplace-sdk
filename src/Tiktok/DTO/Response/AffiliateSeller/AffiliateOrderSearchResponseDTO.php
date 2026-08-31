<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `POST /affiliate_seller/202410/orders/search`.
 *
 * `totalCount` conta SKU-orders, nao pedidos — um pedido com 3 SKUs pesa 3.
 */
final class AffiliateOrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(AffiliateOrder::class)]
        public readonly ?array $orders = null,
        public readonly ?string $nextPageToken = null,
        // contagem de SKU-orders, nao de pedidos
        public readonly ?int $totalCount = null,
    ) {}
}
