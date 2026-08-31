<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Par peso+dimensao. Aparece DUAS vezes no goods, e a diferenca importa:
 *   - `merchant_declaration_info`: o que o seller DECLAROU.
 *   - `warehouse_confirmation_info`: o que o armazem MEDIU no primeiro inbound.
 * Divergencia entre os dois e' o que gera cobranca retroativa de tarifa de
 * armazenagem/frete — vale reconciliar os dois antes de fechar o custo do SKU.
 */
final class FbtGoodsMeasurement implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtWeight $weight = null,
        public readonly ?FbtDimension $dimension = null,
    ) {}
}
