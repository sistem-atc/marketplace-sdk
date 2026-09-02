<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AuthorizedPayments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * Invoice Search resource. Represents the paginated result set returned when searching for
 * subscription invoices. Contains matching invoice records along with pagination metadata.
 */
final class AuthorizedPaymentSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search paging. */
        public readonly ?Paging $paging = null,

        /** Search results. @var list<AuthorizedPaymentResponseDTO>|null */
        #[ArrayOf(AuthorizedPaymentResponseDTO::class)]
        public readonly ?array $results = null,
    ) {}
}
