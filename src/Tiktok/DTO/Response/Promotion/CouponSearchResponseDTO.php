<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Página do Search Coupon List (`data` de /promotion/202406/coupons/search).
 *
 * Paginação é por cursor: repita passando `nextPageToken` até ele voltar "".
 * `totalCount` é o total do filtro, não o tamanho da página.
 *
 * @property list<Coupon>|null $coupons
 */
final class CouponSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalCount = null,
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(Coupon::class)]
        public readonly ?array $coupons = null,
    ) {}
}
