<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Creator recem-conectado a loja no periodo da cota.
 */
final class CreatorOutreachConnectRecord implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $creatorOpenId = null,
        public readonly ?bool $hasSentImMessage = null,
        public readonly ?bool $hasSentInvitation = null,
        public readonly ?bool $isPaired = null,
    ) {}
}
