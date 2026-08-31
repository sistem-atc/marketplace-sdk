<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Replica do produto em outro mercado (`replicated_products[]`).
 *
 * O `productId` daqui e' o id da replica NAQUELE mercado (loja `shopId`), nao o
 * id consultado — cada mercado tem produto e status proprios, e um pode estar
 * ACTIVE enquanto outro esta' em auditoria.
 */
final class GlobalReplicatedProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $region = null,
        public readonly ?string $shopId = null,
        public readonly ?string $productId = null,
        public readonly ?string $productStatus = null,
    ) {}
}
