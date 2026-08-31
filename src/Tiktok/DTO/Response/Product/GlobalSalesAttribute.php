<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo de venda que define a variante (cor, tamanho...).
 * No GET vem completo (id + name + value_id + value_name + imagem);
 * nas respostas de create/edit vem so' os ids (ver GlobalCreatedSalesAttribute).
 */
final class GlobalSalesAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $valueId = null,
        public readonly ?string $valueName = null,
        public readonly ?GlobalImage $skuImg = null,
    ) {}
}
