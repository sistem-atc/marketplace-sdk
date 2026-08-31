<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.integrated_platform_statuses` do webhook TYPE 37 — auditoria do mesmo
 * produto na plataforma nativamente integrada (hoje so' TOKOPEDIA).
 *
 * O nome e' plural mas doc e exemplo mandam um OBJETO unico, nao lista.
 */
final class IntegratedPlatformStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // TOKOPEDIA
        public readonly ?string $platform = null,
        // NONE | AUDITING | FAILED | APPROVED (sem PRE_APPROVED aqui)
        public readonly ?string $status = null,
    ) {}
}
