<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\SupplyChain;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de POST /supply_chain/{v}/packages/sync.
 *
 * ARMADILHA: é lote PARCIAL. O envelope volta `code: 0` mesmo quando parte dos
 * pacotes falhou — só os ids em `success_packages` foram confirmados; o resto
 * está em `errors[]` e precisa reenvio. Nunca dar o lote por confirmado sem
 * comparar `success_packages` com o que foi enviado.
 */
final class ConfirmPackageShipmentResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Lista de ids (strings), não objetos. */
        public readonly ?array $successPackages = null,
        #[ArrayOf(PackageSyncError::class)]
        public readonly ?array $errors = null,
    ) {}
}
