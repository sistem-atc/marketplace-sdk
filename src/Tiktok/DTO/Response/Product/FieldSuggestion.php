<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Sugestoes para um campo do produto (`suggestions[]`). Field: TITLE | DESCRIPTION. */
final class FieldSuggestion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $field = null,
        #[ArrayOf(SuggestionText::class)]
        public readonly ?array $items = null,
    ) {}
}
