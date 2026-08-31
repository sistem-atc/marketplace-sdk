<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de POST /affiliate_partner/{v}/orders/search (programa TAP).
 *
 * Mesmo limite de 3 meses de histórico do CAP.
 */
final class TapAffiliateOrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(TapAffiliateSkuOrder::class)]
        public readonly ?array $skuOrders = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
