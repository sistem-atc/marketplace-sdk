<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202405/target_collaborations/search.
 *
 * @property list<TargetCollaboration>|null $targetCollaborations
 */
final class TargetCollaborationSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalCount = null,
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(TargetCollaboration::class)]
        public readonly ?array $targetCollaborations = null,
    ) {}
}
