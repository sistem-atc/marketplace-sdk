<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Exigencias de hazmat/validade de UM SKU do TikTok Shop.
 *
 * A chave aqui e' `ttsSkuId` (SKU do Shop), nao goods_id: este endpoint e'
 * justamente o que se consulta ANTES de criar o goods, pra saber se o SKU vai
 * exigir declaracao de perigoso e controle de lote. Suplemento costuma cair em
 * `isLotCode=true` + `isExpirationManagement=true`.
 */
final class FbtHazmatAndExpirationItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $ttsSkuId = null,
        public readonly ?bool $isHazmat = null,
        public readonly ?bool $isLotCode = null,
        public readonly ?bool $isExpirationManagement = null,
        public readonly ?FbtExpirationBaseInfo $expBaseInfo = null,
    ) {}
}
