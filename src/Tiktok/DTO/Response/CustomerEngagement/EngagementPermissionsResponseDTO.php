<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de GET /customer_engagement/{v}/permissions.
 *
 * Chamar ANTES de montar campanha: a feature é liberada loja a loja pelo
 * TikTok, e sem `FUNDAMENTAL` autorizado o resto da família falha.
 */
final class EngagementPermissionsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(EngagementFeature::class)]
        public readonly ?array $features = null,
    ) {}
}
