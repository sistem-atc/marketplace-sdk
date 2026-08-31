<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Top-3 faixas etarias dos seguidores + genero predominante.
 */
final class TopFollowerDemographics implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // AGE_RANGE_18_24 | AGE_RANGE_25_34 | AGE_RANGE_35_44 | AGE_RANGE_45_54 | AGE_RANGE_55_AND_ABOVE
        public readonly ?array $ageRanges = null,
        public readonly ?FollowerGenderShare $majorGender = null,
    ) {}
}
