<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Devolução da entrega (`deliveries[].returns[]`) — rara. `extras` shape livre.
 *
 * @property array<string, mixed>|null $extras
 */
final class DeliveryReturn implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $date = null,
        public readonly ?string $externalId = null,
        public readonly ?array $extras = null,
    ) {}
}
