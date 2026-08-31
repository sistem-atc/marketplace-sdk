<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resultado, por par (goods, sku), do bind/unbind. Sucesso e' por linha. */
final class FbtGoodsSkuRelationResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $ttsGoodsId = null,
        public readonly ?string $ttsSkuId = null,
        public readonly ?bool $isSuccess = null,
        public readonly ?string $failReason = null,
    ) {}
}
