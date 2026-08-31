<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco `click_to_order_rate` das lives: a MESMA taxa medida em duas granulacoes
 * de pedido — por SKU e por pedido principal (main order = uma compra do cliente,
 * que pode conter varios SKUs). Fracao STRING, nao percentual.
 * Reusado por Shop LIVE Minute Performance e Shop LIVE Products Performance.
 */
final class LiveClickToOrderRate implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $skuOrderCtor = null,
        public readonly ?string $mainOrderCtor = null,
    ) {}
}
