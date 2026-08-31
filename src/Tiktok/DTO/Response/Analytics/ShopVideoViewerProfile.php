<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Perfil da audiencia do video, repetido por `type`: VIEWERS (quem assistiu) e
 * NEW_FOLLOWER (quem passou a seguir depois de assistir). Sao recortes
 * DIFERENTES da mesma base — nao some as porcentagens entre os dois.
 */
final class ShopVideoViewerProfile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        #[ArrayOf(ShopVideoGenderDistribution::class)]
        public readonly ?array $genderDistribution = null,
        #[ArrayOf(ShopVideoAgeDistribution::class)]
        public readonly ?array $ageDistribution = null,
        #[ArrayOf(ShopVideoCountryDistribution::class)]
        public readonly ?array $countryDistribution = null,
    ) {}
}
