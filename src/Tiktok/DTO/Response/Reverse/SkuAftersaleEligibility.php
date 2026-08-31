<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Elegibilidade agrupada por SKU (`sku_eligibility[]`).
 *
 * O agrupamento e' por SKU, nao por linha de pedido: o mesmo SKU comprado em
 * duas linhas cai num unico item, e as linhas aparecem dentro de
 * `lineItemEligibility[].orderLineList`.
 *
 * @property list<LineItemAftersaleEligibility>|null $lineItemEligibility
 */
final class SkuAftersaleEligibility implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $skuId = null,
        #[ArrayOf(LineItemAftersaleEligibility::class)]
        public readonly ?array $lineItemEligibility = null,
    ) {}
}
