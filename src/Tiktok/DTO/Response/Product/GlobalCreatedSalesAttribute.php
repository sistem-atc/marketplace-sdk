<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo de venda devolvido por create/edit/publish — SO' os ids.
 * Quando voce mandou nome customizado na requisicao, estes ids sao NOVOS,
 * gerados pelo TikTok: guarde-os pra editar a variante depois.
 */
final class GlobalCreatedSalesAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $valueId = null,
    ) {}
}
