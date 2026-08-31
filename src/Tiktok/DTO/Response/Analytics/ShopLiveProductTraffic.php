<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `traffic` de um produto dentro de UMA live.
 *
 * `produtClicks` e' TYPO DA API (falta o "c"). Mapeado por #[JsonKey] pra chave
 * crua correta; a propriedade em PHP fica escrita direito.
 */
final class ShopLiveProductTraffic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $productImpressions = null,
        public readonly ?string $ctr = null,
        public readonly ?int $addToCartCount = null,
        public readonly ?LiveClickToOrderRate $clickToOrderRate = null,
        public readonly ?LiveGpm $gpm = null,
        #[JsonKey('produt_clicks')]
        public readonly ?int $productClicks = null,
    ) {}
}
