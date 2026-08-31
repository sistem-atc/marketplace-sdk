<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de GMV do SKU por tipo de conteudo. Aqui `amount`/`currency` sao CHAPADOS
 * no proprio item (nao ha' objeto `gmv` aninhado) — por isso nao reusa MonetaryValue.
 */
final class ShopSkuGmvBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $type = null,
    ) {}
}
