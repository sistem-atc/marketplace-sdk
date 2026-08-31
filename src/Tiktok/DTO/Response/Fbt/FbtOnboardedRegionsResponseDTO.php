<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `GET /fbt/202409/merchants/onboarded_regions`.
 *
 * Este e' o teste barato de "essa loja usa FBT?": se o seller NAO esta'
 * onboarded, o TikTok devolve `data` VAZIO (nao um erro), entao
 * `onboardedRegions` vem null. Chame isto antes de qualquer rotina de
 * inventario/inbound pra nao gastar quota em loja que nao opera FBT.
 *
 * @property list<FbtOnboardedRegion>|null $onboardedRegions
 */
final class FbtOnboardedRegionsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtOnboardedRegion::class)]
        public readonly ?array $onboardedRegions = null,
    ) {}
}
