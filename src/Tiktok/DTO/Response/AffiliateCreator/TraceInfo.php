<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Rastro do link que originou o pedido.
 *
 * `type=GENERAL` -> `id` e' o {eid} que voce injetou no sharing_link;
 * `type=SPECIFIC` -> `id` e' o publisher_id.
 */
final class TraceInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
    ) {}
}
