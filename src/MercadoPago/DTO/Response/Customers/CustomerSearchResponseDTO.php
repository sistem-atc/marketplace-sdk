<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * Paginated search result for customers in the MercadoPago platform. Returned by the customer
 * search endpoint. Contains pagination metadata and an array of matching customer records. Use
 * filters such as email or identification to narrow results.
 */
final class CustomerSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Pagination metadata (offset, limit, total). */
        public readonly ?Paging $paging = null,

        /** Array of customer records matching the search criteria. @var list<CustomerResponseDTO>|null */
        #[ArrayOf(CustomerResponseDTO::class)]
        public readonly ?array $results = null,
    ) {}
}
