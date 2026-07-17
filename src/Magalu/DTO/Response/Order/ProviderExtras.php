<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Flags do provedor logístico (`shipping.provider.extras`).
 * `isFulfillment`/`isMle` = pedido é Magalu Entregas (fulfillment) — o gatilho
 * das 2 NFes (venda + reintegração). Ver [[fulfillment-classificacao-cfop-gate-mp]].
 */
final class ProviderExtras implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isFulfillment = null,
        public readonly ?bool $isMle = null,
        public readonly ?string $shippingName = null,
        public readonly ?string $shippingType = null,
    ) {}
}
