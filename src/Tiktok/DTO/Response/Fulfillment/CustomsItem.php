<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de pedido na consulta de exigencia alfandegaria.
 *
 * Quando `isVirtualUnboxing` ou `isTemporaryListing` e' true, o que declarar
 * na alfandega NAO e' este sku e sim o que vem em `relatedItemDetail`.
 *
 * @property list<RelatedItemDetail>|null $relatedItemDetail
 */
final class CustomsItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderLineId = null,
        public readonly ?string $productId = null,
        public readonly ?string $skuId = null,
        public readonly ?bool $isVirtualUnboxing = null,
        public readonly ?bool $isTemporaryListing = null,
        #[ArrayOf(RelatedItemDetail::class)]
        public readonly ?array $relatedItemDetail = null,
    ) {}
}
