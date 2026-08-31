<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202309/packages/search.
 *
 * Paginacao por cursor opaco: `nextPageToken` vazio/ausente = fim. NAO
 * derive offset de `totalCount` — ele e' so' informativo.
 *
 * @property list<PackageSummary>|null $packages
 */
final class PackageSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(PackageSummary::class)]
        public readonly ?array $packages = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
