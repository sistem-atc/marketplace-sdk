<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Canal de venda (`channel`). O `extras.alias` é o que separa Magalu de
 * Netshoes — os dois vêm pela MESMA API (ver agente bunker-mp-magalu).
 */
final class Channel implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?ChannelExtras $extras = null,
        public readonly ?Marketplace $marketplace = null,
    ) {}
}
