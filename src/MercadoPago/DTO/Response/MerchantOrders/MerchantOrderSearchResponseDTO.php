<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Search resource. Represents the paginated result set returned when searching for
 * merchant orders. Contains a list of matching merchant orders along with pagination metadata.
 */
final class MerchantOrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search elements. @var list<MerchantOrderResponseDTO>|null */
        #[ArrayOf(MerchantOrderResponseDTO::class)]
        public readonly ?array $elements = null,

        /** Search next offset. */
        public readonly ?int $nextOffset = null,

        /** Search total. */
        public readonly ?int $total = null,
    ) {}
}
