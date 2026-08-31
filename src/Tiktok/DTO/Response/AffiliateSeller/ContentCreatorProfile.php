<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Perfil resumido do creator que promove o produto.
 *
 * O identificador estavel e' o `creatorOpenId` — `username` muda.
 */
final class ContentCreatorProfile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $nickname = null,
        public readonly ?int $followerCount = null,
        public readonly ?AffiliateImage $avatar = null,
        public readonly ?string $creatorOpenId = null,
    ) {}
}
