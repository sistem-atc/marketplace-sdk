<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido Shopee — resposta de `/api/v2/order/get_order_detail`.
 *
 * PARCIAL POR DESIGN: a Shopee só devolve os campos pedidos em
 * `response_optional_fields`, e cada chamador do SDK pede um conjunto
 * diferente. Todo campo é nullable e o AutoHydrate ignora chave ausente —
 * um pedido buscado só com `invoice_data` hidrata um DTO com o resto null.
 * Nunca trate null aqui como "a Shopee não tem o dado"; pode só não ter
 * sido pedido.
 *
 * Datas são epoch em segundos (int), não ISO — diferente do ML.
 *
 * @property list<OrderItem>|null $itemList
 * @property list<OrderPackage>|null $packageList
 */
final class OrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderSn = null,
        public readonly ?string $orderStatus = null,
        public readonly ?string $bookingSn = null,
        public readonly ?string $region = null,
        public readonly ?string $currency = null,
        public readonly ?float $totalAmount = null,
        public readonly ?bool $cod = null,
        // Datas: epoch em SEGUNDOS.
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        public readonly ?int $payTime = null,
        public readonly ?int $shipByDate = null,
        public readonly ?int $daysToShip = null,
        public readonly ?int $pickupDoneTime = null,
        public readonly ?int $returnRequestDueDate = null,
        // Comprador
        public readonly ?int $buyerUserId = null,
        public readonly ?string $buyerUsername = null,
        public readonly ?string $buyerCpfId = null,
        public readonly ?string $messageToSeller = null,
        public readonly ?RecipientAddress $recipientAddress = null,
        // Frete / logística
        public readonly ?float $actualShippingFee = null,
        public readonly ?bool $actualShippingFeeConfirmed = null,
        public readonly ?float $estimatedShippingFee = null,
        public readonly ?float $reverseShippingFee = null,
        public readonly ?string $shippingCarrier = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?int $orderChargeableWeightGram = null,
        public readonly ?bool $splitUp = null,
        public readonly ?bool $goodsToDeclare = null,
        public readonly ?bool $isBuyerShopCollection = null,
        public readonly ?bool $advancePackage = null,
        // Full/FBS — marca o pedido como fulfillment da Shopee.
        public readonly ?string $fulfillmentFlag = null,
        // Cancelamento
        public readonly ?bool $canFullCancelOrder = null,
        public readonly ?bool $canPartialCancelOrder = null,
        public readonly ?int $buyerPreferenceForPartialCancellation = null,
        public readonly ?string $cancelReason = null,
        public readonly ?string $cancelBy = null,
        public readonly ?string $buyerCancelReason = null,
        // Dropshipper: sempre null no corpus BR, mas a Shopee documenta string.
        public readonly ?string $dropshipper = null,
        public readonly ?string $dropshipperPhone = null,
        // Nota do vendedor
        public readonly ?string $note = null,
        public readonly ?int $noteUpdateTime = null,
        public readonly ?bool $hotListingOrder = null,
        public readonly ?InvoiceData $invoiceData = null,
        #[ArrayOf(OrderItem::class)]
        public readonly ?array $itemList = null,
        #[ArrayOf(OrderPackage::class)]
        public readonly ?array $packageList = null,
    ) {}
}
