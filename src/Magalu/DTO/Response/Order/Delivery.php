<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Entrega do pedido (`deliveries[]`) — 1 pedido pode ter N entregas (split por
 * seller/logística). É onde moram itens, NFes, rastreio e o flag de fulfillment.
 *
 * @property list<DeliveryItem>|null $items
 * @property list<DeliveryInvoice>|null $invoices
 * @property list<DeliveryEvent>|null $events
 * @property list<DeliveryReturn>|null $returns
 */
final class Delivery implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $code = null,
        public readonly ?string $status = null,
        public readonly ?Amounts $amounts = null,
        public readonly ?Branch $branch = null,
        public readonly ?Seller $seller = null,
        public readonly ?Shipping $shipping = null,
        #[ArrayOf(DeliveryItem::class)]
        public readonly ?array $items = null,
        #[ArrayOf(DeliveryInvoice::class)]
        public readonly ?array $invoices = null,
        #[ArrayOf(DeliveryEvent::class)]
        public readonly ?array $events = null,
        #[ArrayOf(DeliveryReturn::class)]
        public readonly ?array $returns = null,
    ) {}
}
