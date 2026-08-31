<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Envelope do resultado de criacao de goods.
 *
 * @property list<FbtCreateGoodsResult>|null $createResultList
 */
final class FbtCreateGoodsResultInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtCreateGoodsResult::class)]
        public readonly ?array $createResultList = null,
    ) {}
}
