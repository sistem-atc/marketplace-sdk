<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * GET /suggestions/items/{id}/details — sugestão de preço do ML.
 *
 * GOTCHA: o path do webhook price_suggestion (/marketplace/benchmarks/items/{id}/
 * details) dá 403 "Invalid caller.id"; quem responde 200 é este (/suggestions/).
 *
 * `costs` (selling_fees/shipping_fees) e `metadata` (graph/compared_values) ficam
 * crus — shape volátil e volumosa.
 */
final class PriceSuggestionResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $itemId = null,
        public readonly ?string $status = null,
        public readonly ?string $currencyId = null,
        public readonly ?float $ratio = null,
        public readonly ?float $percentDifference = null,
        public readonly ?SuggestionPrice $currentPrice = null,
        public readonly ?SuggestionPrice $suggestedPrice = null,
        public readonly ?SuggestionPrice $lowestPrice = null,
        public readonly ?SuggestionPrice $internalPrice = null,
        public readonly ?SuggestionPrice $externalPrice = null,
        public readonly mixed $applicableSuggestion = null,
        public readonly mixed $costs = null,
        public readonly mixed $metadata = null,
        public readonly ?string $lastUpdated = null,
    ) {}
}
