<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Criador (afiliado) que promove o produto. */
final class Creator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nickName = null,
        public readonly ?string $avatarUrl = null,
        public readonly ?int $followerNum = null,
        public readonly ?string $userName = null,
        /** Id estável do criador entre apps (Open ID). */
        public readonly ?string $creatorOpenId = null,
    ) {}
}
