<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Diagnostico dentro do Check Product Listing.
 *
 * Nao reusa ProductDiagnosis porque aqui a chave e' `suggestions` no PLURAL
 * (nos outros endpoints e' `suggestion`) — apesar de ser um objeto so'.
 * Todo o bloco esta' DEPRECIADO pela doc: use Diagnose and Optimize Product.
 */
final class ListingCheckDiagnosis implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $field = null,
        #[ArrayOf(DiagnosisResult::class)]
        public readonly ?array $diagnosisResults = null,
        public readonly ?DiagnosisSuggestion $suggestions = null,
    ) {}
}
