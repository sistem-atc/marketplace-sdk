<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Controle de lote e validade do goods.
 *
 * TODOS os "*Days" sao CONTAGENS REGRESSIVAS a partir da data de VALIDADE, nao
 * a partir de hoje nem da fabricacao: com validade 01/12 e
 * `inboundCutoffDays=20`, o armazem para de receber em 11/11. Suplemento
 * alimentar cai nas categorias com lote obrigatorio, entao esses numeros
 * decidem quando o envio ao FBT ainda vale a pena.
 */
final class FbtLotExpirationInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isLotControl = null,
        public readonly ?bool $isExpirationManagement = null,
        // Shelf life total do produto, em dias (da fabricacao ao vencimento).
        public readonly ?int $shelfLifeDays = null,
        public readonly ?int $inboundCutoffDays = null,
        public readonly ?int $expirationAlertDays = null,
        public readonly ?int $salesCutoffDays = null,
        // TURN_INTO_DEFECTIVE_INVENTORY | RETURN_TO_SUPPLIER | DISPOSE
        public readonly ?string $handlingMethod = null,
    ) {}
}
