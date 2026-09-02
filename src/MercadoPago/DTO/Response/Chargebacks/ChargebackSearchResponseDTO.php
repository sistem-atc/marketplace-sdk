<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Chargebacks;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * Chargeback Search resource. Represents the paginated result set returned when searching for
 * chargebacks.
 */
final class ChargebackSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search paging. */
        public readonly ?Paging $paging = null,

        /** Search results. @var list<ChargebackResponseDTO>|null */
        #[ArrayOf(ChargebackResponseDTO::class)]
        public readonly ?array $results = null,
    ) {}
}
