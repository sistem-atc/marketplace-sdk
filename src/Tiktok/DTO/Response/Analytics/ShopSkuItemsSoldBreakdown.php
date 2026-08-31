<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de UNIDADES vendidas do SKU por tipo de conteudo.
 *
 * Pegadinha: o campo se chama `amount` mas e' um INT de contagem, nao dinheiro —
 * mesmo nome que o `amount` monetario de `ShopSkuGmvBreakdown`.
 */
final class ShopSkuItemsSoldBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $amount = null,
        public readonly ?string $type = null,
    ) {}
}
