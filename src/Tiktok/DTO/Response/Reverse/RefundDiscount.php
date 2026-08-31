<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Quanto de cada DESCONTO volta pro comprador no reembolso.
 *
 * Importa pro financeiro: o desconto de PLATAFORMA foi bancado pelo TikTok, o
 * de SELLER foi bancado por nos. Reembolsar o total sem separar os dois
 * superestima a perda da venda.
 */
final class RefundDiscount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productPlatformDiscount = null,
        public readonly ?string $productSellerDiscount = null,
        public readonly ?string $shippingFeePlatformDiscount = null,
        public readonly ?string $shippingFeeSellerDiscount = null,
    ) {}
}
