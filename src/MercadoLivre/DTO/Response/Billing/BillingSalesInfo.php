<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `sales_info[]` — a venda que originou a cobrança (order/pack + fees).
 */
final class BillingSalesInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $orderId = null,
        public readonly ?string $operationId = null,
        public readonly ?string $saleDateTime = null,
        public readonly ?string $salesChannel = null,
        public readonly ?string $payerNickname = null,
        public readonly ?string $stateName = null,
        public readonly ?float $transactionAmount = null,
        public readonly ?float $wholesalePrice = null,
        public readonly ?float $financingTransferTotal = null,
        public readonly ?float $financingFee = null,
        // sale_fee e' POLIMORFICO: as vezes float, as vezes objeto
        // {gross,net,rebate,discount,discount_reason} — mixed preserva os dois.
        public readonly mixed $saleFee = null,
    ) {}
}
