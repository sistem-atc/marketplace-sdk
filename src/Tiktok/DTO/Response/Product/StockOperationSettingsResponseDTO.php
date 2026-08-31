<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202604/inventory/operation/settings`.
 *
 * Liga/desliga a reposicao automatica de estoque quando o pedido e' cancelado
 * (so' vale pra pedido FBM). A chamada e' PARCIAL: confira `failedSkuIds`, um
 * code 0 na resposta NAO garante que todos os SKUs foram atualizados.
 */
final class StockOperationSettingsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?StockOperationResult $stockOperation = null,
    ) {}
}
