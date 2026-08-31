<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Envelope do resultado de bind/unbind goods<->SKU.
 *
 * @property list<FbtGoodsSkuRelationResult>|null $operateResultList
 */
final class FbtGoodsSkuRelationInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtGoodsSkuRelationResult::class)]
        public readonly ?array $operateResultList = null,
    ) {}
}
