<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Rastreio de UMA caixa do inbound. So' vem preenchido em inbound de pequeno
 * volume (SMALL_PARCEL); carga fechada (FREIGHT) nao tem waybill por caixa.
 *
 * `relatedNumber` e' o numero da CAIXA ("C0001") a que este rastreio pertence —
 * e' por ele que se casa rastreio x carton.
 */
final class FbtInboundLogisticInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // FEDEX | UPS | DHL | USPS | OTHER
        public readonly ?string $providerName = null,
        public readonly ?string $waybillNumber = null,
        public readonly ?string $relatedNumber = null,
    ) {}
}
