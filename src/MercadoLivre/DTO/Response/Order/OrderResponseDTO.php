<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Buyer;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Seller;

/**
 * RAIZ da resposta de um pedido ML — GET /orders/{id} (OrderMethods::get) e cada
 * elemento de GET /orders/search->results (OrderMethods::search).
 *
 * Ponto ÚNICO de parse do pedido: a árvore inteira é tipada (buyer/seller/
 * order_items[].item/payments[]/shipping/taxes/context/feedback/cancel_detail).
 * Consumidores usam `->orderItems[0]->item->sellerSku`, `->payments[0]->marketplaceFee`,
 * `->buyer->id`, `->shipping->id` etc. em vez de array cru.
 *
 * @property list<OrderItem> $orderItems
 * @property list<OrderPayment> $payments
 * @property list<Mediation> $mediations
 * @property array<int|string, mixed> $tags
 * @property array<int|string, mixed> $staticTags
 * @property array<int|string, mixed> $relatedOrders
 */
final class OrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<OrderItem>  $orderItems
     * @param  list<OrderPayment>  $payments
     * @param  list<Mediation>  $mediations
     * @param  array<int|string, mixed>  $tags
     * @param  array<int|string, mixed>  $staticTags
     * @param  array<int|string, mixed>  $relatedOrders
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $status = null,
        public readonly mixed $statusDetail = null,
        public readonly ?int $packId = null,
        public readonly ?string $buyingMode = null,
        public readonly ?string $currencyId = null,
        public readonly ?float $totalAmount = null,
        public readonly ?float $paidAmount = null,
        public readonly ?float $shippingCost = null,
        public readonly ?bool $fulfilled = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $dateClosed = null,
        public readonly ?string $lastUpdated = null,
        public readonly ?string $dateLastUpdated = null,
        public readonly ?string $dateCreatedTtl = null,
        public readonly ?string $expirationDate = null,
        public readonly ?string $manufacturingEndingDate = null,
        public readonly ?string $comment = null,
        public readonly ?string $pickupId = null,
        public readonly ?Coupon $coupon = null,
        public readonly ?Buyer $buyer = null,
        public readonly ?Seller $seller = null,
        #[ArrayOf(OrderItem::class)]
        public readonly array $orderItems = [],
        #[ArrayOf(OrderPayment::class)]
        public readonly array $payments = [],
        public readonly ?OrderShipping $shipping = null,
        public readonly ?OrderTaxes $taxes = null,
        public readonly ?OrderContext $context = null,
        public readonly ?OrderFeedback $feedback = null,
        public readonly ?CancelDetail $cancelDetail = null,
        public readonly ?OrderRequest $orderRequest = null,
        #[ArrayOf(Mediation::class)]
        public readonly array $mediations = [],
        public readonly array $tags = [],
        public readonly array $staticTags = [],
        public readonly array $relatedOrders = [],
    ) {}
}
