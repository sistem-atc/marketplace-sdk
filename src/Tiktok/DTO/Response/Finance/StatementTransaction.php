<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma transacao do extrato financeiro (`statement_transactions[]` do
 * /finance/{v}/orders/{id}/statement_transactions).
 *
 * E a fonte das TAXAS e do REPASSE (settlement) do pedido TikTok:
 *   revenueAmount    = receita bruta reconhecida
 *   feeAmount        = total de taxas
 *   settlementAmount = liquido repassado ao vendedor
 * O detalhamento por tipo de taxa esta nos ~50 campos *_amount (comissao,
 * frete, impostos, afiliado...).
 *
 * DINHEIRO E STRING (padrao TikTok — ver Order\OrderPayment). Tipar float
 * comeria a casa decimal ("0.00" -> "0") e quebraria o roundtrip lossless. O
 * consumidor faz (float) so no que soma.
 *
 * `skuStatementTransactions[]` quebra os mesmos numeros por SKU.
 *
 * @property list<SkuStatementTransaction>|null $skuStatementTransactions
 */
final class StatementTransaction implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $actualReturnShippingFeeAmount = null,
        public readonly ?string $actualShippingFeeAmount = null,
        public readonly ?string $adjustmentAmount = null,
        public readonly ?string $affiliateAdsCommissionAmount = null,
        public readonly ?string $affiliateCommissionAmount = null,
        public readonly ?string $affiliateCommissionBeforePit = null,
        public readonly ?string $affiliatePartnerCommissionAmount = null,
        public readonly ?string $afterSellerDiscountsSubtotalAmount = null,
        public readonly ?string $currency = null,
        public readonly ?string $customerOrderRefundAmount = null,
        public readonly ?string $customerPaidShippingFeeAmount = null,
        public readonly ?string $customerPaidShippingFeeRefundAmount = null,
        public readonly ?string $customerPaymentAmount = null,
        public readonly ?string $customerRefundAmount = null,
        public readonly ?string $customerShippingFeeAmount = null,
        public readonly ?string $customerShippingFeeOffsetAmount = null,
        public readonly ?string $fbmShippingCostAmount = null,
        public readonly ?string $fbtFulfillmentFeeAmount = null,
        public readonly ?string $fbtFulfillmentFeeReimbursementAmount = null,
        public readonly ?string $fbtShippingCostAmount = null,
        public readonly ?string $feeAmount = null,
        public readonly ?string $grossSalesAmount = null,
        public readonly ?string $grossSalesRefundAmount = null,
        public readonly ?string $id = null,
        public readonly ?string $isrIncomeTaxAmount = null,
        public readonly ?string $ivaVatAmount = null,
        public readonly ?string $netSalesAmount = null,
        public readonly ?string $pitAmount = null,
        public readonly ?string $platformCommissionAmount = null,
        public readonly ?string $platformDiscountAmount = null,
        public readonly ?string $platformDiscountRefundAmount = null,
        public readonly ?string $platformRefundSubsidyAmount = null,
        public readonly ?string $platformShippingFeeDiscountAmount = null,
        public readonly ?string $promoShippingIncentiveAmount = null,
        public readonly ?string $referralFeeAmount = null,
        public readonly ?string $refundAdministrationFeeAmount = null,
        public readonly ?string $refundShippingCostDiscountAmount = null,
        public readonly ?string $retailDeliveryFeeAmount = null,
        public readonly ?string $retailDeliveryFeePaymentAmount = null,
        public readonly ?string $retailDeliveryFeeRefundAmount = null,
        public readonly ?string $returnShippingFeeAmount = null,
        public readonly ?string $revenueAmount = null,
        public readonly ?string $salesTaxAmount = null,
        public readonly ?string $salesTaxPaymentAmount = null,
        public readonly ?string $salesTaxRefundAmount = null,
        public readonly ?string $sellerDiscountAmount = null,
        public readonly ?string $sellerDiscountRefundAmount = null,
        public readonly ?string $settlementAmount = null,
        public readonly ?string $shippingCostAmount = null,
        public readonly ?string $shippingCostDiscountAmount = null,
        public readonly ?string $shippingFeeAmount = null,
        public readonly ?string $shippingFeeSubsidyAmount = null,
        public readonly ?string $shippingInsuranceFeeAmount = null,
        public readonly ?string $signatureConfirmationFeeAmount = null,
        public readonly ?string $statementId = null,
        // epoch em SEGUNDOS (int), nao dinheiro — nao e' string.
        public readonly ?int $statementTime = null,
        public readonly ?string $status = null,
        public readonly ?string $transactionFeeAmount = null,
        #[ArrayOf(SkuStatementTransaction::class)]
        public readonly ?array $skuStatementTransactions = null,
    ) {}
}
