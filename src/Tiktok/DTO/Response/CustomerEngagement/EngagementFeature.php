<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Permissão de uma feature de engajamento pra loja. */
final class EngagementFeature implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /**
         * FUNDAMENTAL | CUSTOM_MSG | ... — `FUNDAMENTAL` é pré-requisito de
         * todas as outras: sem ele autorizado, nenhuma API de engajamento
         * responde.
         */
        public readonly ?string $name = null,
        public readonly ?bool $isAuthorized = null,
    ) {}
}
