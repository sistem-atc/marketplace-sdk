<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202411/products/diagnose_optimize`.
 *
 * Roda o diagnostico ANTES de publicar (product_id e' opcional). Pedir
 * DESCRIPTION em `optimization_fields` pode levar >10s pra responder.
 */
final class DiagnoseOptimizeResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ListingQuality $listingQuality = null,
        #[ArrayOf(ProductDiagnosis::class)]
        public readonly ?array $diagnoses = null,
    ) {}
}
