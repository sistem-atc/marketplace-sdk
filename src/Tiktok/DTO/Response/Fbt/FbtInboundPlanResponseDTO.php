<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202602/create_update_inbound_plan`.
 *
 * Create e update sao o MESMO endpoint: sem `plan_id` no corpo cria, com
 * `plan_id` atualiza. Sem `idempotent_key`, um retry de rede vira plano
 * duplicado — e cada plano vira remessa fisica.
 */
final class FbtInboundPlanResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtInboundPlan $inboundPlan = null,
    ) {}
}
