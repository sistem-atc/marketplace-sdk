<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Rateio do frete DE VOLTA (comprador -> seller) entre as 3 partes.
 *
 * `sellerPaidReturnShippingFeeAmount` e' custo nosso e nao aparece no
 * `refundAmount` — quem so' olha o reembolso subestima o prejuizo da devolucao.
 */
final class ReturnShippingFee implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sellerPaidReturnShippingFeeAmount = null,
        public readonly ?string $buyerPaidReturnShippingFeeAmount = null,
        public readonly ?string $platformPaidReturnShippingFeeAmount = null,
    ) {}
}
