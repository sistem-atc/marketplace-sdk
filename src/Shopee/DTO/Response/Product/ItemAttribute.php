<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo do anúncio (`attribute_list[]`) — é o que alimenta o
 * listing_attributes keyed-by-MP do PIM.
 *
 * @property list<AttributeValue>|null $attributeValueList
 */
final class ItemAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $attributeId = null,
        public readonly ?string $originalAttributeName = null,
        // A Shopee documenta display_* (nome ja' traduzido/exibivel) mas ele
        // NAO aparece no corpus BR — fica mapeado pra nao virar perda se ela
        // passar a mandar. Consumidor deve preferir display e cair no original.
        public readonly ?string $displayAttributeName = null,
        public readonly ?bool $isMandatory = null,
        #[ArrayOf(AttributeValue::class)]
        public readonly ?array $attributeValueList = null,
    ) {}
}
