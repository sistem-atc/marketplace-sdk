<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma linha de cobrança do billing ML (`results[]` do
 * BillingMethods::details) — o ML agrupa em blocos por assunto.
 *
 * Atalhos do que mais se consome:
 *   $c->chargeInfo?->detailAmount      · valor da cobrança
 *   $c->chargeInfo?->detailType        · tipo (comissão, frete, ads…)
 *   $c->salesInfo[0]?->orderId         · pedido que originou
 *   $c->shippingInfo?->shippingId      · envio
 *
 * @property list<BillingSalesInfo> $salesInfo
 * @property list<BillingItemInfo> $itemsInfo
 */
final class BillingCharge implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<BillingSalesInfo>  $salesInfo
     * @param  list<BillingItemInfo>  $itemsInfo
     */
    public function __construct(
        public readonly ?BillingChargeInfo $chargeInfo = null,
        public readonly ?BillingDiscountInfo $discountInfo = null,
        public readonly ?BillingShippingInfo $shippingInfo = null,
        public readonly ?BillingStoreInfo $storeInfo = null,
        #[ArrayOf(BillingSalesInfo::class)]
        public readonly array $salesInfo = [],
        #[ArrayOf(BillingItemInfo::class)]
        public readonly array $itemsInfo = [],
        public readonly mixed $documentInfo = null,
        public readonly mixed $marketplaceInfo = null,
        public readonly mixed $currencyInfo = null,
    ) {}
}
