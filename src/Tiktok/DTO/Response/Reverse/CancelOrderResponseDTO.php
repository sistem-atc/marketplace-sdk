<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Cancel Order (`/return_refund/202602/cancellations`).
 *
 * `cancelStatus = CANCELLATION_REQUEST_SUCCESS` significa que o PEDIDO DE
 * cancelamento foi aceito — nao que o pedido ja' esta' cancelado. O estado
 * final vem depois, pelo webhook (type 11) ou pelo Search Cancellations.
 */
final class CancelOrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cancelId = null,
        public readonly ?string $cancelStatus = null,
    ) {}
}
