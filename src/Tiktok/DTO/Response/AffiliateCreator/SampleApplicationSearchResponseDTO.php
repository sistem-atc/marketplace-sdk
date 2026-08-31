<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202412/sample_applications/search.
 *
 * Sem `total_count`: pagina-se ate' `nextPageToken` vir string vazia.
 *
 * @property list<SampleApplication>|null $sampleApplications
 */
final class SampleApplicationSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(SampleApplication::class)]
        public readonly ?array $sampleApplications = null,
    ) {}
}
