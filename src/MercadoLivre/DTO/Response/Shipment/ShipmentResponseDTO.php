<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Shipment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\NamedRef;

/**
 * RAIZ da resposta de GET /shipments/{id} (ShipmentMethods::getShipment).
 *
 * Ponto ÚNICO de parse do envio: tipa o que o app consome de verdade
 * (status/substatus, logistic_type, tracking, receiver_address, prazos) e
 * mantém como array/mixed os blocos que o ML varia muito e ninguém lê
 * (carrier_info, return_details, shipping_items, substatus_history…).
 * `toArray()` é lossless — serve pra gravar o raw (INSERT-first).
 *
 * @property array<int|string, mixed> $tags
 * @property array<int|string, mixed> $comments
 * @property array<int|string, mixed> $itemsTypes
 * @property array<int|string, mixed> $shippingItems
 * @property array<int|string, mixed> $substatusHistory
 */
final class ShipmentResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>  $tags
     * @param  array<int|string, mixed>  $comments
     * @param  array<int|string, mixed>  $itemsTypes
     * @param  array<int|string, mixed>  $shippingItems
     * @param  array<int|string, mixed>  $substatusHistory
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $status = null,
        public readonly ?string $substatus = null,
        public readonly ?string $mode = null,
        public readonly ?string $type = null,
        public readonly ?string $logisticType = null,
        public readonly ?string $siteId = null,
        public readonly ?int $orderId = null,
        public readonly ?int $senderId = null,
        public readonly ?int $receiverId = null,
        public readonly ?int $customerId = null,
        public readonly ?int $serviceId = null,
        public readonly ?float $baseCost = null,
        public readonly ?float $orderCost = null,
        public readonly ?string $trackingNumber = null,
        public readonly ?string $trackingMethod = null,
        public readonly ?string $returnTrackingNumber = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $lastUpdated = null,
        public readonly ?string $dateFirstPrinted = null,
        // priority_class vem como objeto {id} (não escalar).
        public readonly ?NamedRef $priorityClass = null,
        public readonly ?bool $marketPlace = null,
        public readonly mixed $applicationId = null,
        public readonly mixed $createdBy = null,
        public readonly ?ShipmentAddress $receiverAddress = null,
        public readonly ?ShipmentAddress $senderAddress = null,
        public readonly ?ShipmentOption $shippingOption = null,
        public readonly ?ShipmentStatusHistory $statusHistory = null,
        public readonly mixed $costComponents = null,
        public readonly mixed $sibling = null,
        public readonly mixed $snapshotPacking = null,
        public readonly mixed $carrierInfo = null,
        public readonly mixed $returnDetails = null,
        public readonly mixed $quotation = null,
        public readonly array $tags = [],
        public readonly array $comments = [],
        public readonly array $itemsTypes = [],
        public readonly array $shippingItems = [],
        public readonly array $substatusHistory = [],
    ) {}
}
