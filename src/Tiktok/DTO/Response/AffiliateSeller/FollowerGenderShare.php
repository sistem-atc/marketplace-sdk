<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Genero predominante dos seguidores. `percentage` vem multiplicado por
 * 10.000: 6524 = 65,24% (logo o outro genero e' 34,76%).
 */
final class FollowerGenderShare implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // MALE | FEMALE
        public readonly ?string $gender = null,
        // x10.000: 6524 = 65,24%
        public readonly ?int $percentage = null,
    ) {}
}
