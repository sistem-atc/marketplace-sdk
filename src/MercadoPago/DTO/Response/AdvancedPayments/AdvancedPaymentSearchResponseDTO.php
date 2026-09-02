<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * Advanced Payment Search resource. Represents the paginated result set returned when searching
 * for advanced payments.
 */
final class AdvancedPaymentSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search paging. */
        public readonly ?Paging $paging = null,

        /** Search results. @var list<AdvancedPaymentResponseDTO>|null */
        #[ArrayOf(AdvancedPaymentResponseDTO::class)]
        public readonly ?array $results = null,
    ) {}
}
