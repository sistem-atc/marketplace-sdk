<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ficha da oportunidade.
 *
 * `tags` vem como uma STRING unica separada por virgula (nao lista) e
 * `tagCodes` e' a lista de codigos correspondente, na MESMA ordem — o pareamento
 * e' posicional. `referencePrice` e' faixa textual ("$36.3 - $38.5").
 */
final class OpportunityBasicInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    // camelToSnake devolveria `category_id_level1` (sem o underscore antes
    // do digito) — a API manda `category_id_level_1`. Dai o #[JsonKey].
    public function __construct(
        public readonly ?string $opportunityType = null,
        public readonly ?string $status = null,
        public readonly ?int $createTime = null,
        public readonly ?int $expireTime = null,
        public readonly ?string $title = null,
        #[JsonKey('category_id_level_1')]
        public readonly ?string $categoryIdLevel1 = null,
        #[JsonKey('category_id_level_2')]
        public readonly ?string $categoryIdLevel2 = null,
        #[JsonKey('category_id_level_3')]
        public readonly ?string $categoryIdLevel3 = null,
        #[JsonKey('category_name_level_1')]
        public readonly ?string $categoryNameLevel1 = null,
        #[JsonKey('category_name_level_2')]
        public readonly ?string $categoryNameLevel2 = null,
        #[JsonKey('category_name_level_3')]
        public readonly ?string $categoryNameLevel3 = null,
        public readonly ?string $referencePrice = null,
        public readonly ?array $referenceImages = null,
        public readonly ?string $brandInfo = null,
        public readonly ?string $rewardInfo = null,
        public readonly ?string $recomInfo = null,
        public readonly ?string $tags = null,
        public readonly ?array $tagCodes = null,
        public readonly ?string $externalProductId = null,
        public readonly ?string $referenceSource = null,
    ) {}
}
