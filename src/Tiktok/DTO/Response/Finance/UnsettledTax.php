<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `fee_tax_breakdown.tax` — os IMPOSTOS estimados da transacao nao liquidada.
 *
 * Sao impostos ESTRANGEIROS recolhidos pela plataforma (sales tax dos EUA, VAT,
 * SST/GST, cedular do Mexico). Nao confundir com o imposto brasileiro da NF-e:
 * nada aqui alimenta apuracao fiscal no BR — numa loja BR vem tudo "0".
 *
 * DINHEIRO E STRING (padrao TikTok).
 */
final class UnsettledTax implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // sales_tax = payment - refund (a API ja' entrega os tres).
        public readonly ?string $salesTaxAmount = null,
        public readonly ?string $salesTaxPaymentAmount = null,
        public readonly ?string $salesTaxRefundAmount = null,
        // VAT / import VAT / duty / clearance: so' pedido cross-border.
        public readonly ?string $vatAmount = null,
        public readonly ?string $importVatAmount = null,
        public readonly ?string $customsDutyAmount = null,
        public readonly ?string $customsClearanceAmount = null,
        // SST = Malasia; GST = Singapura (bens de baixo valor importados).
        public readonly ?string $sstAmount = null,
        public readonly ?string $gstAmount = null,
        public readonly ?string $salesTaxReferralFeeAmount = null,
        public readonly ?string $smartPromotionFeeTaxAmount = null,
        // Imposto cedular de Guanajuato (Mexico) — sem sufixo `_amount`.
        public readonly ?string $cedularTax = null,
    ) {}
}
