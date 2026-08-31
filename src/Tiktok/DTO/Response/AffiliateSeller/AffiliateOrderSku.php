<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU de um pedido de afiliado — ONDE MORA A ORIGEM DA COMISSAO.
 *
 * E' a linha que explica o `affiliate_commission_amount` do extrato: qual
 * creator, qual colaboracao/campanha, qual taxa e quanto ja virou definitivo.
 *
 * DUAS PEGADINHAS DE UNIDADE:
 * 1. As taxas (`commissionRate`, `partnerCommissionRate`,
 *    `shopAdsCommissionRate`) vem em CENTESIMOS DE POR CENTO como STRING:
 *    "1550" = 15,50%. Nao e' fracao nem percentual direto.
 * 2. `estimated*` e' previsao sobre a venda bruta; `actual*` e' o que sobra
 *    depois de devolucao/reembolso. Para conciliar repasse use o `actual`.
 *
 * `commissionRate` so' vem quando o pedido nasceu DIRETO do seu creator; via
 * parceiro/agencia o valor esta em `partnerCommissionRate`.
 *
 * `status`, `refundedQuantity` e `returnedQuantity` estao DEPRECIADOS pela
 * doc (nao retornam valor) — mapeados assim mesmo porque a regra do SDK e'
 * zero perda de campo. Use `settlementStatus` e `fullyReturn`.
 */
final class AffiliateOrderSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $skuId = null,
        // Unknown | AWAITING PAYMENT | To-SETTLE | SETTLED | INELIGIBLE
        public readonly ?string $settlementStatus = null,
        public readonly ?string $openCollaborationId = null,
        public readonly ?string $targetCollaborationId = null,
        // campanha TAP (agencia/parceiro)
        public readonly ?string $campaignId = null,
        public readonly ?string $creatorUsername = null,
        public readonly ?AffiliateMoney $price = null,
        public readonly ?int $quantity = null,
        // SHOP | VIDEO | LIVE | PRE_LIVE | PROMOTION_PAGE | LINKSHARE
        public readonly ?string $contentType = null,
        public readonly ?string $contentId = null,
        public readonly ?string $productId = null,
        // ex.: "Tiered commission"
        public readonly ?string $commissionModel = null,
        // ex.: "3.0 OR 5.0" — as faixas configuradas
        public readonly ?string $commissionTierSetting = null,
        // centesimos de %: "1550" = 15,50%
        public readonly ?string $commissionRate = null,
        public readonly ?string $partnerCommissionRate = null,
        public readonly ?string $shopAdsCommissionRate = null,
        // preco * quantidade, antes de devolucao
        public readonly ?AffiliateMoney $estimatedCommissionBase = null,
        public readonly ?AffiliateMoney $estimatedPaidShopAdsCommission = null,
        public readonly ?AffiliateMoney $estimatedPaidCommission = null,
        public readonly ?AffiliateMoney $estimatedPaidPartnerCommission = null,
        // preco * (quantidade - devolvida)
        public readonly ?AffiliateMoney $actualCommissionBase = null,
        public readonly ?AffiliateMoney $actualPaidCommission = null,
        public readonly ?AffiliateMoney $actualPaidPartnerCommission = null,
        public readonly ?AffiliateMoney $actualPaidShopAdsCommission = null,
        // parte do bonus do creator que o seller banca
        public readonly ?AffiliateMoney $estimatedCofundedCreatorBonusAmount = null,
        public readonly ?AffiliateMoney $actualCofundedCreatorBonusAmount = null,
        // DEPRECIADO pela doc — use fullyReturn
        public readonly ?int $refundedQuantity = null,
        // DEPRECIADO pela doc — use fullyReturn
        public readonly ?int $returnedQuantity = null,
        // "Yes" quando devolvido por inteiro: nao gera comissao
        public readonly ?string $fullyReturn = null,
    ) {}
}
