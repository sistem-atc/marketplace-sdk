<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tarifa de colocacao cobrada pelo FBT pra receber a carga.
 *
 * DINHEIRO E' STRING ("13.50"). So' o metodo ONE_HUB tem tarifa (D2FC vem
 * zerado/ausente) — e' o custo que decide entre mandar tudo pra um hub ou
 * fracionar direto pros centros de distribuicao. Esse valor entra no custo de
 * aquisicao do estoque no ERP, nao no custo do pedido.
 */
final class FbtPlacementFee implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        // USD | CNY | EUR | GBP
        public readonly ?string $currency = null,
    ) {}
}
