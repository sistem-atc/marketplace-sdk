<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/product/202507/products/{product_id}/replicated_products`.
 *
 * Vale so' para loja no modo LOCAL_REPLICATION (o produto nasce local e e'
 * replicado). No modo GLOBAL_PUBLISHING as publicacoes saem do produto global
 * e vivem em `publish_result` — nao aqui. Ver `getGlobalListingRules()` para
 * saber em qual modo a loja esta'.
 *
 * @property list<GlobalReplicatedProduct>|null $replicatedProducts
 */
final class GlobalReplicatedProductsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalReplicatedProduct::class)]
        public readonly ?array $replicatedProducts = null,
    ) {}
}
