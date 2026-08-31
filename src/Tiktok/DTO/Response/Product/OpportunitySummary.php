<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Oportunidade na LISTAGEM — shape diferente do detalhe: aqui os campos de
 * mercado e categoria vem achatados na raiz do item, sem os objetos
 * `basic_info`/`market_data`. Nao reaproveite o DTO de detalhe aqui.
 */
final class OpportunitySummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    // camelToSnake devolveria `category_id_level1` (sem o underscore antes
    // do digito) — a API manda `category_id_level_1`. Dai o #[JsonKey].
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?array $referenceImages = null,
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
        public readonly ?bool $isCollected = null,
        public readonly ?string $queryWords = null,
        public readonly ?string $opportunityType = null,
        public readonly ?string $brandName = null,
        public readonly ?int $createTime = null,
        public readonly ?int $expireTime = null,
        public readonly ?string $estMarketSalesVolume = null,
        public readonly ?string $estMarketGmv = null,
        public readonly ?string $searchVolume = null,
        public readonly ?string $competitorProductCount = null,
        public readonly ?string $videoViewPv = null,
        public readonly ?string $tags = null,
        public readonly ?array $tagCodes = null,
        public readonly ?string $rewardInfo = null,
        public readonly ?string $externalProductId = null,
        public readonly ?string $referenceSource = null,
    ) {}
}
