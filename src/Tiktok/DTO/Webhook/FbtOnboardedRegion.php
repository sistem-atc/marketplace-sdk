<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Item de `onboarded_regions` do webhook type 22. */
final class FbtOnboardedRegion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo de regiao FBT habilitada (ex.: "GB"). */
        public readonly ?string $regionCode = null,
    ) {}
}
