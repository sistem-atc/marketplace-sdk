<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/product/202309/categories/{category_id}/global_attributes`.
 *
 * E' o irmao GLOBAL do Get Attributes (categoria local): mesma ideia, mas cada
 * atributo carrega a matriz de obrigatoriedade POR MERCADO, que a versao local
 * nao tem.
 *
 * @property list<GlobalCategoryAttribute>|null $attributes
 */
final class GlobalCategoryAttributesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalCategoryAttribute::class)]
        public readonly ?array $attributes = null,
    ) {}
}
