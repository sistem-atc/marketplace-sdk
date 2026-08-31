<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Template de tabela de medidas (`size_chart[]`). */
final class SizeChart implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $templateId = null,
        public readonly ?string $templateName = null,
        #[ArrayOf(SizeChartImage::class)]
        public readonly ?array $images = null,
    ) {}
}
