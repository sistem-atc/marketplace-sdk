<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` dos dois geradores de link em lote:
 *   POST /affiliate_creator/202505/affiliate_sharing_links/general_publishers/generate_batch
 *   POST /affiliate_creator/202504/affiliate_sharing_links/publisher/{publisher_id}/generate_batch
 *
 * Mesma shape de resposta — por isso um DTO so'.
 *
 * @property list<SharingLink>|null $sharingLinks
 * @property list<FailedMaterial>|null $failedMaterials
 */
final class SharingLinkBatchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SharingLink::class)]
        public readonly ?array $sharingLinks = null,
        #[ArrayOf(FailedMaterial::class)]
        public readonly ?array $failedMaterials = null,
    ) {}
}
