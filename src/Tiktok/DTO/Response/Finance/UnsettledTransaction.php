<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma transacao AINDA NAO LIQUIDADA (`data.transactions[]` de
 * GET /finance/{v}/orders/unsettled).
 *
 * E' o repasse FUTURO: o dinheiro que o TikTok ja' reconhece dever mas ainda
 * nao pagou. Todo valor aqui e' ESTIMATIVA (prefixo `est*`) e MUDA ate' a
 * liquidacao — principalmente o frete, que so' fecha quando a transportadora
 * pesa o pacote. Pra numero definitivo, use o StatementTransaction.
 *
 * `estimatedSettlement` e' STRING de proposito, nao int: enquanto o pedido nao
 * e' entregue o TikTok manda uma politica em texto ("x days after delivery") e
 * so' depois passa a mandar o epoch. Tipar int transformaria o texto em 0.
 *
 * `type` = ORDER pra venda; qualquer outro valor e' AJUSTE (CHARGE_BACK,
 * PLATFORM_PENALTY, LOGISTICS_REIMBURSEMENT, GMV_PAYMENT_FOR_ADS...). Cada
 * transacao traz OU `orderId` OU `adjustmentId` — nunca os dois; o ajuste liga
 * no pedido ressarcido por `adjustmentOrderId`.
 *
 * DINHEIRO E STRING (padrao TikTok — tipar float comeria a casa decimal).
 */
final class UnsettledTransaction implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        // Unico valor possivel hoje: UNSETTLED.
        public readonly ?string $status = null,
        public readonly ?string $currency = null,
        public readonly ?string $estimatedSettlement = null,
        public readonly ?string $unsettledReason = null,
        // epoch em SEGUNDOS. `orderDeliveryTime` so' vem se ja' entregue —
        // e' o gatilho que faz a estimativa de frete virar valor real.
        public readonly ?int $orderCreateTime = null,
        public readonly ?int $orderDeliveryTime = null,
        public readonly ?string $orderId = null,
        public readonly ?string $adjustmentId = null,
        public readonly ?string $adjustmentOrderId = null,
        public readonly ?string $estAdjustmentAmount = null,
        // Formula da API: revenue - shipping_cost - fee_tax - adjustment.
        public readonly ?string $estSettlementAmount = null,
        public readonly ?string $estRevenueAmount = null,
        public readonly ?UnsettledRevenueBreakdown $revenueBreakdown = null,
        public readonly ?string $estShippingCostAmount = null,
        public readonly ?UnsettledShippingCostBreakdown $shippingCostBreakdown = null,
        public readonly ?string $estFeeTaxAmount = null,
        public readonly ?UnsettledFeeTaxBreakdown $feeTaxBreakdown = null,
    ) {}
}
