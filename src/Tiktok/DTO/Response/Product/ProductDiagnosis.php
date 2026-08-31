<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Diagnostico de um campo do produto (TITLE / DESCRIPTION / IMAGE / ATTRIBUTE /
 * SIZE_CHART), com os problemas achados e a sugestao de melhoria.
 */
final class ProductDiagnosis implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $field = null,
        #[ArrayOf(DiagnosisResult::class)]
        public readonly ?array $diagnosisResults = null,
        public readonly ?DiagnosisSuggestion $suggestion = null,
    ) {}
}
