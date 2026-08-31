<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Colaboracao direcionada — convite nominal do vendedor pro creator.
 *
 * `status`: LIVE | EXPIRED | DELETED | ENDED.
 *
 * @property list<TargetCollaborationProduct>|null $products
 */
final class TargetCollaboration implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $status = null,
        #[ArrayOf(TargetCollaborationProduct::class)]
        public readonly ?array $products = null,
    ) {}
}
