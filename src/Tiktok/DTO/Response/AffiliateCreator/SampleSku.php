<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU elegivel (ou nao) pra ser pedido como amostra.
 *
 * `unavailableReason` explica o bloqueio: IS_PREORDER, IS_GIFT, OUT_OF_STOCK,
 * EXCEED_CB_PRICE_THRESHOLD, ALREADY_APPLYED (typo do TikTok, mantido).
 *
 * @property list<SkuSaleProperty>|null $saleProperties
 */
final class SampleSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $salePropertyValueIds = null,
        public readonly ?MoneyAmount $price = null,
        #[ArrayOf(SkuSaleProperty::class)]
        public readonly ?array $saleProperties = null,
        public readonly ?bool $isAvailable = null,
        public readonly ?string $unavailableReason = null,
    ) {}
}
