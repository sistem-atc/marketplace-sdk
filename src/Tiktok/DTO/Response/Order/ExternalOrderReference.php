<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Um item de `data.orders[]` da busca por referencia externa: o id do pedido
 * NO TIKTOK mais a referencia que gravamos nele.
 *
 * Este DTO e' o vinculo que interessa — dado o NOSSO id, ele devolve o id do
 * TikTok de forma deterministica.
 */
final class ExternalOrderReference implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id do pedido no TIKTOK. */
        public readonly ?string $id = null,
        public readonly ?ExternalOrder $externalOrder = null,
    ) {}
}
