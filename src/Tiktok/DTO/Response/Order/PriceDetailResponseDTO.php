<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Decomposicao de preco do pedido — `/order/{v}/orders/{id}/price_detail`.
 *
 * POR QUE IMPORTA: e' aqui que o desconto bancado pela PLATAFORMA aparece
 * separado do bancado pelo VENDEDOR (`subtotalDeductionPlatform` vs
 * `subtotalDeductionSeller`). Sem esta chamada, o unico lugar onde esse numero
 * existia era o extrato financeiro — e quando o extrato vinha vazio, o
 * financeiro ficava com saldo em aberto sem explicacao.
 *
 * Dinheiro e' STRING (padrao TikTok). `taxRate` tambem — e' fracao ("0.021"),
 * nao percentual.
 *
 * @property list<PriceDetailLineItem>|null $lineItems
 */
final class PriceDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        /** O que o comprador viu de total. */
        public readonly ?string $total = null,
        /** O que o comprador efetivamente pagou. */
        public readonly ?string $payment = null,
        public readonly ?string $skuListPrice = null,
        public readonly ?string $skuSalePrice = null,
        public readonly ?string $subtotal = null,
        /** Desconto que NOS bancamos. */
        public readonly ?string $subtotalDeductionSeller = null,
        /** Desconto que o TIKTOK bancou — some do que o comprador paga, mas o canal repassa. */
        public readonly ?string $subtotalDeductionPlatform = null,
        public readonly ?string $subtotalTaxAmount = null,
        public readonly ?string $voucherDeductionPlatform = null,
        public readonly ?string $voucherDeductionSeller = null,
        public readonly ?string $shippingListPrice = null,
        public readonly ?string $shippingSalePrice = null,
        public readonly ?string $shippingFeeDeductionSeller = null,
        public readonly ?string $shippingFeeDeductionPlatform = null,
        public readonly ?string $shippingFeeDeductionPlatformVoucher = null,
        public readonly ?string $taxAmount = null,
        /** Fracao, nao percentual: "0.021" = 2,1%. */
        public readonly ?string $taxRate = null,
        public readonly ?string $netPriceAmount = null,
        public readonly ?string $codFee = null,
        public readonly ?string $codFeeNetAmount = null,
        public readonly ?string $skuGiftOriginalPrice = null,
        public readonly ?string $skuGiftNetPrice = null,
        public readonly ?string $distanceShippingFee = null,
        public readonly ?string $distanceFee = null,
        #[ArrayOf(PriceDetailLineItem::class)]
        public readonly ?array $lineItems = null,
    ) {}
}
