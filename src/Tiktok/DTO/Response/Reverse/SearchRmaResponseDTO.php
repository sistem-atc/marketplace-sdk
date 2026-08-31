<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Search Return Merchandise Authorization
 * (`/return_refund/202604/rma/search`).
 *
 * Mesma paginacao por token opaco do Search Aftersales.
 *
 * @property list<ReturnMerchandiseAuthorization>|null $returnMerchandiseAuthorizations
 */
final class SearchRmaResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ReturnMerchandiseAuthorization::class)]
        public readonly ?array $returnMerchandiseAuthorizations = null,
        public readonly ?int $totalCount = null,
        public readonly ?string $nextPageToken = null,
    ) {}
}
