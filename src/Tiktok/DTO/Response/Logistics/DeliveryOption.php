<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Opção de entrega de um armazém — item de `data.delivery_options[]`.
 *
 * O `id` daqui é o `delivery_option_id` que o Get Shipping Providers exige na
 * URL, e o mesmo que aparece em `deliveryOptionId` do pedido.
 *
 * Com `scope=PRODUCT` a API devolve SÓ `id` e `name`; todos os limites vêm
 * nulos — não é ausência de restrição, é ausência do dado.
 *
 * @property list<string>|null $platform
 */
final class DeliveryOption implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        // STANDARD | EXPRESS | ECONOMY | SEND_BY_SELLER
        public readonly ?string $type = null,
        public readonly ?string $description = null,
        public readonly ?DeliveryOptionDimensionLimit $dimensionLimit = null,
        public readonly ?DeliveryOptionWeightLimit $weightLimit = null,
        // TIKTOK_SHOP | TOKOPEDIA — lista de strings crua, sem DTO
        public readonly ?array $platform = null,
    ) {}
}
