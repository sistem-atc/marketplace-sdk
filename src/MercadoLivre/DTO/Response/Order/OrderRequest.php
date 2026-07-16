<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedidos de alteração/devolução associados (`order_request`).
 *
 * @property array<int|string, mixed>|null $change
 * @property array<int|string, mixed>|null $return
 */
final class OrderRequest implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>|null  $change
     * @param  array<int|string, mixed>|null  $return
     */
    public function __construct(
        public readonly ?array $change = null,
        public readonly ?array $return = null,
    ) {}
}
