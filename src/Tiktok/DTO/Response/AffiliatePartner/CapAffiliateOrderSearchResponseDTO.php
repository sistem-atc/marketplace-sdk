<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de POST /affiliate_partner/{v}/cap_order/search.
 *
 * A API só devolve 3 MESES de histórico — apuração mais antiga que isso tem
 * que sair de dado já gravado, não dá pra rebuscar.
 */
final class CapAffiliateOrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Uma linha por SKU, não por pedido. */
        #[ArrayOf(CapAffiliateSkuOrder::class)]
        public readonly ?array $skuOrders = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
