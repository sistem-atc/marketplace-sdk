<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Um STATEMENT (settlement/repasse) do TikTok — `data.statements[]` de
 * GET /finance/{v}/statements. É o repasse REAL (nível de payout), vs o
 * statement POR PEDIDO (getOrderStatementTransactions).
 *
 *   settlementAmount = liquido repassado ao vendedor neste statement
 *   revenueAmount    = receita bruta
 *   feeAmount        = total de taxas
 *   paymentStatus / paymentId = status e id do pagamento (rastro do repasse)
 *
 * DINHEIRO É STRING (padrão TikTok — preserva a casa decimal no roundtrip).
 */
final class Statement implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $statementTime = null,
        public readonly ?string $settlementAmount = null,
        public readonly ?string $currency = null,
        public readonly ?string $revenueAmount = null,
        public readonly ?string $feeAmount = null,
        public readonly ?string $adjustmentAmount = null,
        public readonly ?string $netSalesAmount = null,
        public readonly ?string $shippingCostAmount = null,
        public readonly ?string $paymentStatus = null,
        public readonly ?string $paymentId = null,
    ) {}
}
