<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Envio do pedido (`shipping`). No payload de /orders vem só o `id` — o detalhe
 * (logistic_type, status, endereço) mora em /shipments/{id} (ShipmentMethods).
 */
final class OrderShipping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
    ) {}
}
