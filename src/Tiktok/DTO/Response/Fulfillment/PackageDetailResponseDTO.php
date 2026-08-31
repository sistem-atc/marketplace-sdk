<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pacote completo — `data` de GET /fulfillment/202309/packages/{package_id}.
 *
 * O maior payload do grupo fulfillment. Pontos que surpreendem:
 *
 * 1. PESO E MEDIDA SAO STRING ("1.2"), igual dinheiro no pedido.
 * 2. `orders[].skus[]` esta' [Deprecated] na doc; o vinculo vivo com o item
 *    do pedido e' `orderLineItemIds`.
 * 3. `packageStatus` do pacote NAO e' o status do PEDIDO — um pedido
 *    COMPLETED pode ter pacote CANCELLED (extraviado) e vice-versa.
 * 4. `packageSubStatus` vem com caixa inconsistente da propria API
 *    ("Created", "RTS_success", "TTS"): compare case-insensitive.
 *
 * Datas sao epoch em SEGUNDOS.
 *
 * @property list<PackageOrder>|null $orders
 * @property list<string>|null $orderLineItemIds
 * @property list<string>|null $bizExtensionTags
 */
final class PackageDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $packageId = null,
        #[ArrayOf(PackageOrder::class)]
        public readonly ?array $orders = null,
        /** TO FULFILL | PROCESSING | FULFILLING | COMPLETED | CANCELLED */
        public readonly ?string $packageStatus = null,
        /** CREATED | STOCKING | RTSING | RTS_success | HBW | HBWING | TTS | Delivery | Ship_exception | ISSUE */
        public readonly ?string $packageSubStatus = null,
        /** PACKAGE_DAMAGAED | PACKAGE_LOST | RETURNED_TO_SHIPPER | PACKAGE_SCRAP */
        public readonly ?string $shipExceptionReason = null,
        /** DEFAULT | COMBINE | SPLIT */
        public readonly ?string $splitAndCombineTag = null,
        public readonly ?bool $hasMultiSkus = null,
        /** BUYER_UNNOTED | BUYER_NOTED */
        public readonly ?string $noteTag = null,
        public readonly ?string $shippingProviderName = null,
        public readonly ?string $shippingProviderId = null,
        /** TIKTOK (etiqueta da plataforma) | SELLER (envio proprio) */
        public readonly ?string $shippingType = null,
        public readonly ?string $deliveryOptionName = null,
        public readonly ?string $deliveryOptionId = null,
        public readonly ?string $trackingNumber = null,
        // Cross-border: o rastreio da ULTIMA milha e' outro numero.
        public readonly ?string $lastMileTrackingNumber = null,
        public readonly ?PickupSlot $pickupSlot = null,
        public readonly ?int $createTime = null,
        /** PICKUP | DROP_OFF */
        public readonly ?string $handoverMethod = null,
        /** @var list<string>|null */
        public readonly ?array $orderLineItemIds = null,
        public readonly ?PackageAddress $recipientAddress = null,
        public readonly ?PackageAddress $senderAddress = null,
        public readonly ?PackageWeight $weight = null,
        public readonly ?PackageDimension $dimension = null,
        public readonly ?int $updateTime = null,
        public readonly ?PackageInsurance $insurance = null,
        /** @var list<string>|null NON_MCF etc. */
        public readonly ?array $bizExtensionTags = null,
        public readonly ?string $warehouseId = null,
    ) {}
}
