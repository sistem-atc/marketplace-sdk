<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Quanto conteudo cada creator produziu pro produto na colaboracao aberta.
 */
final class CreatorContentDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ContentCreatorProfile $creatorProfile = null,
        public readonly ?int $videoCount = null,
        public readonly ?int $liveCount = null,
        // NORMAL | TERMINATING
        public readonly ?string $promotionStatus = null,
        public readonly ?int $promotionEndTime = null,
    ) {}
}
