<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Detalhe do erro parcial. A chave presente varia por endpoint: pacote
 * (`package_id`), lista de pedidos (`order_ids`) ou pedido unico
 * (`order_id`). O DTO cobre as tres — nunca vem tudo junto.
 */
final class FulfillmentErrorDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $packageId = null,
        public readonly ?string $orderId = null,
        /** @var list<string>|null */
        public readonly ?array $orderIds = null,
    ) {}
}
