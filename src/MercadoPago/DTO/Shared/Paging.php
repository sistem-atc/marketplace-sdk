<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents pagination metadata returned by MercadoPago search/list endpoints. Included in
 * paginated API responses (e.g. PaymentSearch) to indicate the total result count and current
 * page position.
 */
final class Paging implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Total number of results matching the search criteria. */
        public readonly int|string|null $total = null,

        /** Total number of pages available. */
        public readonly ?int $totalPages = null,

        /** Maximum number of results returned per page. */
        public readonly ?int $limit = null,

        /** Number of results skipped from the beginning of the result set. */
        public readonly ?int $offset = null,
    ) {}
}
