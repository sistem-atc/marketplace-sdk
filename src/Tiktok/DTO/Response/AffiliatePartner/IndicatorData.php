<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * KPIs do produto na campanha.
 *
 * O par estimated/actual é a distinção que define o número: `estimated_*` conta
 * pedidos PAGOS (inclui os que voltaram) e `actual_*` desconta devolução e
 * reembolso. Quem apurar comissão a pagar usa `actual_*`; `estimated_*` só
 * serve de previsão. Todos os campos são STRING, inclusive as contagens.
 */
final class IndicatorData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Pagos = actual + devolvidos. */
        public readonly ?string $paidOrderNum = null,
        public readonly ?string $actualOrderNum = null,
        public readonly ?string $estimatedAmount = null,
        /** GMV de verdade (líquido de devolução). */
        public readonly ?string $actualAmount = null,
        public readonly ?string $estimatedPartnerCommission = null,
        public readonly ?string $actualPartnerCommission = null,
        public readonly ?string $creatorSalesNum = null,
        public readonly ?string $collaboratedCreatorsNum = null,
        public readonly ?string $promotedCreatorNum = null,
        public readonly ?string $sampleRequestedCreatorNum = null,
    ) {}
}
