<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `GET /product/202405/products/diagnoses`.
 *
 * Versao em lote e por produto JA publicado do diagnose_optimize.
 * `product_ids` vai na QUERY separado por virgula.
 */
final class ProductDiagnosesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(DiagnosedProduct::class)]
        public readonly ?array $products = null,
    ) {}
}
