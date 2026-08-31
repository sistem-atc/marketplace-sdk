<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Condicao que torna um atributo (ou uma certificacao) OBRIGATORIO num mercado
 * (`requirement_conditions[]`).
 *
 * Le-se: "no mercado `region`, se o vendedor escolher o valor
 * `attribute_value_id` para o atributo `attribute_id`, entao o item que carrega
 * esta condicao passa a ser exigido". Basta UMA condicao bater.
 *
 * O mesmo shape aparece em Get Global Attributes e em Get Global Category
 * Rules — por isso a classe e' compartilhada pelos dois.
 */
final class GlobalAttributeRequirementCondition implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $region = null,
        /** VALUE_ID_MATCH e' o unico tipo publicado ate' agora. */
        public readonly ?string $conditionType = null,
        public readonly ?string $attributeId = null,
        public readonly ?string $attributeValueId = null,
    ) {}
}
