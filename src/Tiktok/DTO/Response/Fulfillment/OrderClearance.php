<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Exigencia alfandegaria de UM pedido.
 *
 * `bizType` e `shipmentType` sao INT (enum numerico da API, nao string):
 * bizType 1=FS, 2=POP; shipmentType 1=CROSS_BORDER, 2=LOCAL.
 *
 * @property list<CustomsItem>|null $itemList
 */
final class OrderClearance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        public readonly ?bool $needFillClearance = null,
        public readonly ?bool $alreadyFilledClearance = null,
        /** Alpha-2 do pais de destino. */
        public readonly ?string $targetCountry = null,
        /** 1 = FS | 2 = POP */
        public readonly ?int $bizType = null,
        /** 1 = CROSS_BORDER | 2 = LOCAL */
        public readonly ?int $shipmentType = null,
        #[ArrayOf(CustomsItem::class)]
        public readonly ?array $itemList = null,
    ) {}
}
