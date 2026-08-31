<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Pais/regiao em que o seller esta' habilitado no FBT (ISO 3166-1 alpha-2). */
final class FbtOnboardedRegion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $regionCode = null,
    ) {}
}
