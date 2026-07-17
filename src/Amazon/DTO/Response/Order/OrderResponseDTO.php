<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Pedido Amazon — header de `GET /orders/v0/orders/{id}` (o `payload`).
 *
 * PascalCase (SP-API): AmazonOrderId, OrderStatus, OrderTotal. Ver
 * UsesPascalCaseKeys. Siglas que a conversão automática erra (`IsISPU`,
 * `IsSoldByAB`) usam #[JsonKey].
 *
 * DINHEIRO é STRING (`OrderTotal.Amount`). Datas são ISO-8601. PII (endereço/
 * comprador) vem parcial/mascarada sem RDT.
 *
 * @property list<PaymentDetail>|null $paymentExecutionDetail
 * @property list<string>|null $paymentMethodDetails
 */
final class OrderResponseDTO implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amazonOrderId = null,
        public readonly ?string $sellerOrderId = null,
        public readonly ?string $orderStatus = null,
        public readonly ?string $orderType = null,
        public readonly ?string $purchaseDate = null,
        public readonly ?string $lastUpdateDate = null,
        public readonly ?string $earliestShipDate = null,
        public readonly ?string $latestShipDate = null,
        public readonly ?string $earliestDeliveryDate = null,
        public readonly ?string $latestDeliveryDate = null,
        public readonly ?string $fulfillmentChannel = null,
        public readonly ?string $salesChannel = null,
        public readonly ?string $shipServiceLevel = null,
        public readonly ?string $shipmentServiceLevelCategory = null,
        public readonly ?string $easyShipShipmentStatus = null,
        public readonly ?string $electronicInvoiceStatus = null,
        public readonly ?string $marketplaceId = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $sellerDisplayName = null,
        public readonly ?int $numberOfItemsShipped = null,
        public readonly ?int $numberOfItemsUnshipped = null,
        public readonly ?Money $orderTotal = null,
        public readonly ?Address $shippingAddress = null,
        public readonly ?Address $defaultShipFromLocationAddress = null,
        public readonly ?BuyerInfo $buyerInfo = null,
        public readonly ?TaxInfo $marketplaceTaxInfo = null,
        public readonly ?AutomatedShippingSettings $automatedShippingSettings = null,
        // Flags booleanas
        public readonly ?bool $hasRegulatedItems = null,
        public readonly ?bool $isAccessPointOrder = null,
        public readonly ?bool $isBusinessOrder = null,
        public readonly ?bool $isGlobalExpressEnabled = null,
        public readonly ?bool $isPremiumOrder = null,
        public readonly ?bool $isPrime = null,
        // Siglas: camelToPascal erraria (IsSoldByAb/IsIspu) — declaradas.
        #[JsonKey('IsSoldByAB')]
        public readonly ?bool $isSoldByAb = null,
        #[JsonKey('IsISPU')]
        public readonly ?bool $isIspu = null,
        // IsReplacementOrder vem ora bool ora string "false" -> mixed
        public readonly mixed $isReplacementOrder = null,
        #[ArrayOf(PaymentDetail::class)]
        public readonly ?array $paymentExecutionDetail = null,
        public readonly ?array $paymentMethodDetails = null,
    ) {}
}
