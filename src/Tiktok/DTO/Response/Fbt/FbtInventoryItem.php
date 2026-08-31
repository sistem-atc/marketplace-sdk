<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Saldo de um goods num armazem FBT (`data.inventory[]`).
 *
 * A granularidade e' (goods x armazem): o MESMO goods aparece varias vezes, uma
 * por `fbtWarehouseId`. Somar sem agrupar duplica o saldo.
 *
 * `inTransitQuantity` e' o estoque em transito — inclui reposicao inbound,
 * devolucao de cliente e retorno de entrega frustrada. No ERP essa quantidade
 * ja' saiu do CD proprio por nota de REMESSA mas ainda nao chegou: e' o mesmo
 * limbo do "a caminho do Full" do ML. Nao contabilize como disponivel.
 */
final class FbtInventoryItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtInventoryGoods $goods = null,
        public readonly ?string $fbtWarehouseId = null,
        public readonly ?int $inTransitQuantity = null,
        public readonly ?FbtOnHandDetail $onHandDetail = null,
    ) {}
}
