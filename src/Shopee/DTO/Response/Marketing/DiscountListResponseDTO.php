<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/discount/get_discount_list` — paginação por `page_no`
 * 1-BASED (não offset nem cursor): pagine enquanto `more` for true.
 *
 * @property list<DiscountSummary>|null $discountList
 */
final class DiscountListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(DiscountSummary::class)]
        public readonly ?array $discountList = null,
        public readonly ?bool $more = null,
    ) {}
}
