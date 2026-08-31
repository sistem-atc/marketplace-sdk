<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.audit` do webhook TYPE 37.
 *
 * DIVERGENCIA doc x exemplo: a tabela documenta `pre_approved_reasons`
 * (lista de strings) e o exemplo oficial manda `pre_approved_reason` (string
 * singular). Os DOIS estao aqui — o payload real pode vir de qualquer jeito e
 * nenhum dos dois pode sumir no roundtrip.
 */
final class ProductAuditInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $preApprovedReasons */
    public function __construct(
        // NONE | AUDITING | FAILED | PRE_APPROVED | APPROVED
        public readonly ?string $status = null,
        // Forma DOCUMENTADA: lista. KYC_PENDING | RESTRICTED_CATEGORY_PENDING
        public readonly ?array $preApprovedReasons = null,
        // Forma do EXEMPLO oficial: string singular.
        public readonly ?string $preApprovedReason = null,
    ) {}
}
