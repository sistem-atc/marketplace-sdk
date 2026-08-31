<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de GET /affiliate_creator/202508/profiles — perfil do creator do
 * token (nao aceita parametro: e' sempre "quem sou eu").
 *
 * `sellerType` so' vem se o creator TAMBEM tiver conta de vendedor.
 */
final class CreatorProfileResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?CreatorAvatar $avatar = null,
        public readonly ?string $username = null,
        public readonly ?string $selectionRegion = null,
        public readonly ?string $registerRegion = null,
        public readonly ?string $sellerType = null,
        /** @var list<string>|null */
        public readonly ?array $permissions = null,
        public readonly ?string $userType = null,
        public readonly ?string $creatorUserOpenId = null,
    ) {}
}
