<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Quebra do score SKPP por dimensao (`task_details[]`).
 *
 * `actionSuggestions` so' vem quando `passed` = false.
 */
final class SkppTaskDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $category = null,
        public readonly ?string $name = null,
        public readonly ?string $currentValue = null,
        public readonly ?string $targetValue = null,
        public readonly ?bool $passed = null,
        #[ArrayOf(SkppActionSuggestion::class)]
        public readonly ?array $actionSuggestions = null,
    ) {}
}
