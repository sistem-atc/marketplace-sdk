<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma opcao de colocacao (placement option): como o FBT propoe QUEBRAR a carga
 * entre armazens, e quanto cobra por isso.
 *
 * `placementOptionId` e' o que se devolve no Confirm Inbound Method.
 *
 * @property list<FbtAllocationShipment>|null $allocationShipments
 */
final class FbtInboundMethodDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtAllocationShipment::class)]
        public readonly ?array $allocationShipments = null,
        public readonly ?string $placementOptionId = null,
        public readonly ?FbtPlacementFee $placementFeeEstimate = null,
    ) {}
}
