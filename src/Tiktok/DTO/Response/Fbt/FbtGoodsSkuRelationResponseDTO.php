<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202603/goods/update_goods_sku_relation`.
 *
 * E' o de/para fisico<->anuncio: sem BIND, o estoque existe no armazem mas o
 * anuncio nao vende (o `matched=false` do searchGoods). UN_BIND corta o
 * abastecimento na hora — nao e' operacao de limpeza cosmetica.
 */
final class FbtGoodsSkuRelationResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtGoodsSkuRelationInfo $operateGoodsSkuRelationInfo = null,
    ) {}
}
