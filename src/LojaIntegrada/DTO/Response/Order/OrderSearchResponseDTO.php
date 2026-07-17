<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Wrapper do endpoint `pedido/search/` (Tastypie): meta + objects[].
 * Ver [[marketplace-sdk-dto-pattern]].
 */
final class OrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param OrderResponseDTO[] $objects */
    public function __construct(
        public readonly ?SearchMeta $meta = null,
        #[ArrayOf(OrderResponseDTO::class)]
        public readonly array $objects = [],
    ) {}
}
