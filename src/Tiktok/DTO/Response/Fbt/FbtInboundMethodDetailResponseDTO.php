<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202602/inbound_method_detail`.
 *
 * E' ASSINCRONO: a primeira chamada volta com `placementTaskStatus=Executing` e
 * `info` ainda vazio — repita ate' `Succeed` (ou `Failed`). Note a caixa dos
 * valores: "Executing"/"Succeed"/"Failed" em CamelCase, ao contrario dos enums
 * SCREAMING_CASE do resto da API.
 *
 * `expirationTimestamp` (epoch em segundos, mas STRING): o resultado do
 * placement expira em UM DIA. Passou disso, confirmar falha e e' preciso gerar
 * uma nova task.
 *
 * @property list<FbtInboundMethodDetail>|null $info
 */
final class FbtInboundMethodDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtInboundMethodDetail::class)]
        public readonly ?array $info = null,
        public readonly ?string $placementTaskStatus = null,
        public readonly ?string $expirationTimestamp = null,
    ) {}
}
