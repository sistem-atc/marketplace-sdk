<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Search Cancellations (`/return_refund/202602/cancellations/search`).
 *
 * ATENCAO: neste endpoint a paginacao vai na QUERY STRING (page_size,
 * page_token, sort_field, sort_order) e os filtros no BODY — o inverso do
 * Search Aftersales/RMA, que poem tudo no body.
 *
 * @property list<Cancellation>|null $cancellations
 */
final class SearchCancellationsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(Cancellation::class)]
        public readonly ?array $cancellations = null,
        public readonly ?int $totalCount = null,
        public readonly ?string $nextPageToken = null,
    ) {}
}
