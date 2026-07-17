<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Category;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Definição de atributo de uma categoria (GET /categories/{id}/attributes) — é o
 * "schema" que o anúncio precisa preencher pra publicar.
 *
 * `tags` traz flags como `required`/`catalog_required` (shape volátil → cru).
 *
 * @property array<int|string, mixed> $values
 * @property array<int|string, mixed> $allowedUnits
 */
final class CategoryAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>  $values
     * @param  array<int|string, mixed>  $allowedUnits
     */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $valueType = null,
        public readonly ?int $valueMaxLength = null,
        public readonly ?int $relevance = null,
        public readonly ?string $hint = null,
        public readonly ?string $example = null,
        public readonly ?string $type = null,
        public readonly ?string $hierarchy = null,
        public readonly ?string $attributeGroupId = null,
        public readonly ?string $attributeGroupName = null,
        public readonly ?string $defaultUnit = null,
        public readonly ?string $tooltip = null,
        public readonly mixed $tags = null,
        public readonly mixed $valueMaxLengthByType = null,
        public readonly array $values = [],
        public readonly array $allowedUnits = [],
    ) {}

    /** Atributo obrigatório pra publicar (flag `required` em tags). */
    public function isRequired(): bool
    {
        return (bool) data_get($this->tags, 'required', false);
    }
}
