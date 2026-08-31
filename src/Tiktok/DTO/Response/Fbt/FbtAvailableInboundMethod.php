<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Opcao de envio pro armazem FBT.
 *
 * `inboundMethod`:
 *   - D2FC: direto pros centros de distribuicao, PODE SER QUEBRADO em varias
 *     remessas (varios destinos = varias notas de remessa no ERP);
 *   - ONE_HUB: destino unico, com tarifa de colocacao (uma nota so').
 * A escolha nao e' so' logistica: muda quantas notas de remessa a operacao
 * fiscal vai emitir.
 *
 * @property list<FbtTimeWindow>|null $availableTimeWindowList
 * @property list<FbtInboundMethodWarehouse>|null $warehouseList
 */
final class FbtAvailableInboundMethod implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $inboundMethod = null,
        #[ArrayOf(FbtTimeWindow::class)]
        public readonly ?array $availableTimeWindowList = null,
        #[ArrayOf(FbtInboundMethodWarehouse::class)]
        public readonly ?array $warehouseList = null,
        public readonly ?FbtPlacementFee $placementFeeEstimate = null,
    ) {}
}
