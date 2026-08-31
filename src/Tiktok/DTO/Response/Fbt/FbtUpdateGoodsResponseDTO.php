<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202607/goods/update_goods_info`.
 *
 * ATENCAO: o update NAO e' parcial. O corpo exige nome, imagem, peso/dimensao,
 * hazmat e flags de lote — omitir um bloco APAGA o que estava la'. Leia o goods
 * (searchGoods) antes de montar o payload.
 */
final class FbtUpdateGoodsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtUpdateGoodsResult $updateResultInfo = null,
    ) {}
}
