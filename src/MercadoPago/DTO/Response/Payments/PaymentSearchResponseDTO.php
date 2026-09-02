<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * Represents the paginated result of a payment search query in the MercadoPago API. Returned by
 * PaymentClient and contains pagination metadata along with the list of matching payments.
 */
final class PaymentSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Pagination metadata (total, limit, offset). */
        public readonly ?Paging $paging = null,

        /** List of payments matching the search criteria. @var list<PaymentResponseDTO>|null */
        #[ArrayOf(PaymentResponseDTO::class)]
        public readonly ?array $results = null,
    ) {}
}
