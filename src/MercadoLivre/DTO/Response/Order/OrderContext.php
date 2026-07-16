<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Contexto do pedido (`context`) — site, canal e flows de origem.
 *
 * @property array<int|string, mixed> $flows
 */
final class OrderContext implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed> $flows */
    public function __construct(
        public readonly ?string $site = null,
        public readonly ?string $channel = null,
        public readonly ?string $application = null,
        public readonly ?string $productId = null,
        public readonly array $flows = [],
    ) {}
}
