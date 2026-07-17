<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Desconto na listagem (`discount_list[]`) — sem os itens participantes.
 * Pra ver os itens, chame `getDiscount($discountId)`.
 *
 * Datas são epoch em SEGUNDOS.
 */
final class DiscountSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $discountId = null,
        public readonly ?string $discountName = null,
        public readonly ?string $status = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $source = null,
    ) {}
}
