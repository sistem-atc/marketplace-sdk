<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preço de um SKU (`skus[].price`).
 *
 * DINHEIRO É STRING no TikTok (`tax_exclusive_price` = "120", "85.50") — ver
 * Order\OrderPayment. `taxExclusivePrice` é o preço SEM imposto, que é o campo
 * que o monitor de preços usa. Converta com `(float)` no consumidor.
 */
final class SkuPrice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?string $taxExclusivePrice = null,
        public readonly ?string $salePrice = null,
        public readonly ?string $taxInclusivePrice = null,
    ) {}
}
