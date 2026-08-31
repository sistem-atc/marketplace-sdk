<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo built-in da categoria GLOBAL (`attributes[]`).
 *
 * `type` separa dois mundos: SALES_PROPERTY gera VARIACAO (cor, tamanho) e
 * PRODUCT_PROPERTY so' descreve o produto. Os flags
 * `isRequired`/`isMultipleSelection`/`isCustomizable` valem apenas para
 * PRODUCT_PROPERTY.
 *
 * ARMADILHA: a chave da API e' `is_requried` — erro de digitacao do TikTok que
 * esta' no contrato publicado. O #[JsonKey] fixa a grafia errada da API sem
 * contaminar o nome da propriedade; escrever `isRequried` no DTO "funcionaria"
 * por acidente e viraria bug no dia em que a API corrigir.
 *
 * Obrigatoriedade e' POR MERCADO: `isRequired=false` NAO quer dizer opcional —
 * quer dizer "leia `requiredRegions` (exigido sem condicao), `optionalRegions`
 * e `requirementConditions` (exigido se a condicao bater)".
 *
 * @property list<GlobalAttributeValue>|null $values
 * @property list<GlobalAttributeRequirementCondition>|null $requirementConditions
 */
final class GlobalCategoryAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        /** SALES_PROPERTY | PRODUCT_PROPERTY */
        public readonly ?string $type = null,
        #[JsonKey('is_requried')]
        public readonly ?bool $isRequired = null,
        #[ArrayOf(GlobalAttributeValue::class)]
        public readonly ?array $values = null,
        public readonly ?bool $isMultipleSelection = null,
        public readonly ?bool $isCustomizable = null,
        #[ArrayOf(GlobalAttributeRequirementCondition::class)]
        public readonly ?array $requirementConditions = null,
        /** @var list<string>|null */
        public readonly ?array $optionalRegions = null,
        /** @var list<string>|null */
        public readonly ?array $requiredRegions = null,
    ) {}
}
