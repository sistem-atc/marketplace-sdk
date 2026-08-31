<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Item de `products[]` da listagem de status SKPP. */
final class SkppProductStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $title = null,
        public readonly ?string $skppStatus = null,
        public readonly ?string $affiliateProgramStatus = null,
        public readonly ?int $totalScore = null,
        public readonly ?int $targetScore = null,
        #[ArrayOf(SkppReward::class)]
        public readonly ?array $rewards = null,
    ) {}
}
