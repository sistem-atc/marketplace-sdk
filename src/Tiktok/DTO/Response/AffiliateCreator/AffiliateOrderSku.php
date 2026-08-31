<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU de um pedido de afiliado — item de `orders[].skus[]` do
 * /affiliate_creator/202410/orders/search.
 *
 * AQUI HA `quantity` (diferente do /order/{v}/orders do vendedor, onde cada
 * line_item e' 1 unidade).
 *
 * TODAS as taxas (`*Rate`) sao INTEIRO em centesimos de por cento: 3000 = 30%.
 * TODOS os valores (`*Commission*`, `price`, `isr`, `iva`, `pit`) sao
 * MoneyAmount com `amount` STRING ja' formatado na moeda local.
 *
 * `estimated*` = projecao no momento do pedido; `actual*` = pos-liquidacao,
 * ja' descontadas devolucoes (`returnedQuantity`) e reembolsos
 * (`refundedQuantity`).
 *
 * `isr`/`iva` so' pra creator do MEXICO; `pit` so' VN/TH/PH/ID.
 */
final class AffiliateOrderSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $campaignId = null,
        public readonly ?string $openCollaborationId = null,
        public readonly ?string $targetCollaborationId = null,
        public readonly ?string $productName = null,
        public readonly ?string $productId = null,
        public readonly ?MoneyAmount $price = null,
        public readonly ?string $shopName = null,
        // SHOP | VIDEO | LIVE | PRE_LIVE | PROMOTION_PAGE | LINKSHARE
        public readonly ?string $contentType = null,
        public readonly ?string $contentId = null,
        public readonly ?int $quantity = null,
        public readonly ?int $commissionRate = null,
        // Texto livre ("3.0 OR 5.0"): as faixas do modelo de comissao escalonada.
        public readonly ?string $commissionTierSetting = null,
        public readonly ?string $commissionModel = null,
        public readonly ?int $commissionBonusRate = null,
        public readonly ?MoneyAmount $estimatedCommissionBase = null,
        public readonly ?int $standardCommissionRate = null,
        public readonly ?int $shopAdsCommissionRate = null,
        public readonly ?MoneyAmount $estimatedBonusCommission = null,
        public readonly ?MoneyAmount $estimatedStandardCommission = null,
        public readonly ?MoneyAmount $estimatedShopAdsCommission = null,
        public readonly ?MoneyAmount $estimatedCommission = null,
        public readonly ?MoneyAmount $actualCommission = null,
        public readonly ?MoneyAmount $actualBonusCommission = null,
        public readonly ?MoneyAmount $actualCommissionBase = null,
        public readonly ?MoneyAmount $actualStandardCommission = null,
        public readonly ?MoneyAmount $actualShopAdsCommission = null,
        public readonly ?int $returnedQuantity = null,
        public readonly ?int $refundedQuantity = null,
        // Metadado livre que o proprio integrador injeta pra rastrear.
        public readonly ?string $tag = null,
        public readonly ?int $creatorCommissionRewardRate = null,
        public readonly ?MoneyAmount $estimatedCreatorCommissionRewardFee = null,
        public readonly ?MoneyAmount $actualCreatorCommissionRewardFee = null,
        // Impostos retidos na fonte: isr/iva = Mexico, pit = VN/TH/PH/ID.
        public readonly ?MoneyAmount $isr = null,
        public readonly ?MoneyAmount $iva = null,
        public readonly ?MoneyAmount $pit = null,
        public readonly ?MoneyAmount $sharedWithPartner = null,
        public readonly ?TraceInfo $traceInfo = null,
        public readonly ?int $lastUpdateTime = null,
    ) {}
}
