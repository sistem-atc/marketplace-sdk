<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Variação de um anúncio (`response.model[]` do get_model_list).
 *
 * É AQUI que mora o preço/estoque de anúncio com variação — o item-pai vem
 * com priceInfo null (ver ItemResponseDTO::$hasModel).
 *
 * `tierIndex` são ÍNDICES posicionais dentro de `tier_variation[].option_list`
 * — ex.: [0,2] = 1ª opção da 1ª variação + 3ª opção da 2ª. Só faz sentido
 * junto do TierVariation da mesma resposta.
 *
 * @property list<PriceInfo>|null $priceInfo
 * @property list<int>|null $tierIndex
 */
final class Model implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $modelId = null,
        public readonly ?string $modelName = null,
        public readonly ?string $modelSku = null,
        public readonly ?string $modelStatus = null,
        public readonly ?array $tierIndex = null,
        #[ArrayOf(PriceInfo::class)]
        public readonly ?array $priceInfo = null,
        public readonly ?StockInfoV2 $stockInfoV2 = null,
        public readonly ?Dimension $dimension = null,
        public readonly ?PreOrder $preOrder = null,
        // STRING na Shopee, não número.
        public readonly ?string $weight = null,
        public readonly ?string $gtinCode = null,
        public readonly ?bool $hasPromotion = null,
        public readonly ?int $promotionId = null,
        public readonly ?bool $isFulfillmentByShopee = null,
    ) {}
}
