<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Caixa REAL de uma ordem de inbound (`carton_details[]`).
 *
 * So' vem se `include_carton_details=true` for pedido; caso contrario o TikTok
 * omite o bloco inteiro por performance — ausencia aqui NAO significa "sem
 * caixas".
 *
 * Diferente da caixa planejada, aqui a chave e' `carton_no` (uma caixa
 * concreta) e nao ha' `quantity` de caixas.
 *
 * @property list<FbtCartonItem>|null $items
 */
final class FbtInboundCarton implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cartonNo = null,
        // SINGLE_SKU | MULTI_SKU
        public readonly ?string $cartonType = null,
        public readonly ?FbtBoxMeasurements $boxMeasurements = null,
        #[ArrayOf(FbtCartonItem::class)]
        public readonly ?array $items = null,
    ) {}
}
