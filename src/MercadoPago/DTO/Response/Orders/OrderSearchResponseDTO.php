<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * Represents a paginated search result for MercadoPago Orders. Returned by the Orders search
 * endpoint, this resource wraps paging metadata and the list of matching Order resources.
 */
final class OrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Pagination metadata (offset, limit, total). */
        public readonly ?Paging $paging = null,

        /** Array of Order resources matching the search criteria. @var list<OrderResponseDTO>|null */
        #[ArrayOf(OrderResponseDTO::class)]
        public readonly ?array $data = null,
    ) {}
}
