<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Planejado x recebido, por goods e por LOTE.
 *
 * ESTE E' O BLOCO QUE FECHA (OU NAO) COM A NOTA DE REMESSA. `plannedCount` e' o
 * que a nota declarou; `totalReceivedCount` = normal + defective e' o que o
 * armazem aceitou. Divergencia vira quebra de estoque em poder de terceiro e
 * precisa de acerto fiscal — e o `defectiveReceivedCount` nao volta pro saldo
 * vendavel.
 *
 * Se o inbound foi pra um HUB, `normalReceivedCount` so' e' atualizado quando a
 * carga chega ao FC: zero aqui pode significar "ainda em transito interno", nao
 * "recusado".
 *
 * `expirationTime` e' epoch em SEGUNDOS (int) — atencao: no carton o campo
 * equivalente e' STRING.
 */
final class FbtInboundLotReceiveItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $goodsId = null,
        public readonly ?string $lotCode = null,
        public readonly ?int $expirationTime = null,
        public readonly ?int $plannedCount = null,
        public readonly ?int $normalReceivedCount = null,
        public readonly ?int $defectiveReceivedCount = null,
        public readonly ?int $totalReceivedCount = null,
    ) {}
}
