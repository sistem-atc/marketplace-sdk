<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Feedback do pedido (`feedback`) — refs buyer/seller. Os ids daqui alimentam
 * OrderMethods::feedbackById (o sub-endpoint /orders/{id}/feedback dá 403).
 *
 * @property array<int|string, mixed>|null $buyer
 * @property array<int|string, mixed>|null $seller
 */
final class OrderFeedback implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>|null  $buyer
     * @param  array<int|string, mixed>|null  $seller
     */
    public function __construct(
        public readonly ?array $buyer = null,
        public readonly ?array $seller = null,
    ) {}
}
