<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Seller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Dados básicos do grupo de interoperabilidade de produto.
 *
 * `source` é STRING de um código numérico ("1" = US_MX_ONBOARD, "2" = GS_POP)
 * — a API manda o número, não o nome do enum.
 */
final class ShopGroup implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $source = null,
        public readonly ?string $shopGroupName = null,
    ) {}
}
