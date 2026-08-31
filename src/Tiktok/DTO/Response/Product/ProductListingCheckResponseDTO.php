<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202309/products/listing_check`.
 *
 * Valida o payload de criacao SEM criar o produto — vale rodar antes de todo
 * publish. `checkResult` = PASS | FAILED. Os campos `listingQuality` e
 * `diagnoses` estao DEPRECIADOS pela propria doc.
 */
final class ProductListingCheckResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $checkResult = null,
        #[ArrayOf(ListingCheckFailReason::class)]
        public readonly ?array $failReasons = null,
        public readonly ?ListingCheckWarning $warnings = null,
        public readonly ?ListingQuality $listingQuality = null,
        #[ArrayOf(ListingCheckDiagnosis::class)]
        public readonly ?array $diagnoses = null,
        #[ArrayOf(PreCheckResult::class)]
        public readonly ?array $preCheckResults = null,
    ) {}
}
