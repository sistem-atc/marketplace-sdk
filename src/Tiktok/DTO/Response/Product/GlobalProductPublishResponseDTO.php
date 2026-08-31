<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de Publish Global Product.
 *
 * Duas listas com papeis distintos:
 *   - `products`: os produtos LOCAIS criados (com de/para de SKU) — so' vem
 *     pros mercados que deram certo
 *   - `publishResult`: o status POR MERCADO, inclusive os que falharam ou
 *     cairam em DRAFT por erro de validacao
 *
 * A chave e' `publish_result` no SINGULAR aqui, e `publish_results` no
 * plural na resposta de edicao. Nao unifique.
 *
 * @property list<GlobalPublishedProduct>|null $products
 * @property list<GlobalPublishResult>|null $publishResult
 */
final class GlobalProductPublishResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalPublishedProduct::class)]
        public readonly ?array $products = null,
        #[ArrayOf(GlobalPublishResult::class)]
        public readonly ?array $publishResult = null,
    ) {}
}
