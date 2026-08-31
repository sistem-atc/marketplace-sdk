<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Colaboracao que da' direito ao creator promover o produto.
 *
 * `type` vem como TEXTO ("OPEN") no exemplo, mas a doc lista os valores como
 * numeros (1 Open, 2 Target, 5 Partner Campaign, 11 Flat Fee, 12 Collaboration
 * Plus, 13 Affiliate Promotion). Fica STRING pra aceitar as duas formas.
 */
final class ShowcaseCollaboration implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?CollaborationPartner $partner = null,
    ) {}
}
