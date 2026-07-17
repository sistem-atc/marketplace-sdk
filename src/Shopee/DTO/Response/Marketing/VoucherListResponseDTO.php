<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/voucher/get_voucher_list` — paginação por `page_no`
 * 1-BASED: pagine enquanto `more` for true.
 *
 * @property list<Voucher>|null $voucherList
 */
final class VoucherListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(Voucher::class)]
        public readonly ?array $voucherList = null,
        public readonly ?bool $more = null,
    ) {}
}
