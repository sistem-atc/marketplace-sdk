<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `shipping_cost_breakdown` da transacao NAO liquidada: as parcelas que somam
 * o `estShippingCostAmount` — MENOS o `supplementaryComponent`, que e' so'
 * informativo (ver UnsettledShippingCostSupplement).
 *
 * Enquanto o pedido nao e' entregue o frete e' ESTIMATIVA: a transportadora so'
 * cobra depois de pesar/cubar o pacote, entao estes numeros mudam entre a
 * consulta e a liquidacao.
 *
 * DINHEIRO E STRING (padrao TikTok).
 */
final class UnsettledShippingCostBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $actualShippingFeeAmount = null,
        public readonly ?string $shippingFeeDiscountAmount = null,
        public readonly ?string $customerPaidShippingFeeAmount = null,
        public readonly ?string $returnShippingFeeAmount = null,
        // Troca/reposicao de mercadoria: so' Indonesia.
        public readonly ?string $replacementShippingFeeAmount = null,
        public readonly ?string $exchangeShippingFeeAmount = null,
        public readonly ?string $signatureConfirmationFeeAmount = null,
        public readonly ?string $shippingInsuranceFeeAmount = null,
        public readonly ?string $distantShippingFeeAmount = null,
        public readonly ?string $shippingAppServiceFeeAmount = null,
        // Sem sufixo `_amount` na API — nao inventar um (o snake_case do DTO
        // tem que bater com a chave crua).
        public readonly ?string $logisticsServiceFee = null,
        public readonly ?string $fbtOverallMerchantSubsidy = null,
        public readonly ?string $fbtKeyMerchantSubsidy = null,
        public readonly ?UnsettledShippingCostSupplement $supplementaryComponent = null,
        // Programa de reembolso de frete (entrega falha / devolucao).
        public readonly ?string $sfrReimbursement = null,
        public readonly ?string $tiktokShopShippingIncentiveAmount = null,
    ) {}
}
