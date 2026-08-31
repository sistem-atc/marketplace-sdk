<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Certificacao exigida pela categoria global (`product_certifications[]`).
 *
 * NAO confundir com `GlobalCertification`, que e' o documento JA ANEXADO ao
 * produto: aqui e' a REGRA (o que a categoria exige), sem arquivo.
 *
 * Lista vazia significa "categoria nao exige certificacao".
 *
 * @property list<GlobalAttributeRequirementCondition>|null $requirementConditions
 */
final class GlobalProductCertificationRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?bool $isRequired = null,
        /** Imagem de EXEMPLO do documento, nao o documento do vendedor. */
        public readonly ?string $sampleImageUrl = null,
        /** @var list<string>|null */
        public readonly ?array $requiredRegions = null,
        /** @var list<string>|null */
        public readonly ?array $optionalRegions = null,
        #[ArrayOf(GlobalAttributeRequirementCondition::class)]
        public readonly ?array $requirementConditions = null,
    ) {}
}
