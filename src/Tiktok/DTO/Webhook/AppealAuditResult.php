<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.appeal_task.appeal_audit_result[]` do topico 66 — o veredito
 * por INDICADOR: um mesmo recurso pode ter um indicador aprovado e outro
 * rejeitado.
 *
 * `reject_reason` e' opcional na doc (must-return = false) — so' vem no
 * indicador rejeitado.
 *
 * A doc NAO publica os valores possiveis de `indicator_type` nem de `status`;
 * por isso os dois ficam `?string` sem enum.
 */
final class AppealAuditResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indicador da verificacao. Doc nao publica os valores. */
        public readonly ?string $indicatorType = null,
        /** Resultado do indicador. Doc nao publica os valores. */
        public readonly ?string $status = null,
        /** Motivo — so' preenchido quando rejeitado. */
        public readonly ?string $rejectReason = null,
    ) {}
}
