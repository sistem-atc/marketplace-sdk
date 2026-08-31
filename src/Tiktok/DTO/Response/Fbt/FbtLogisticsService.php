<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Servico logistico habilitado num armazem FBT
 * (`data.warehouses[].logistics_services[]`).
 *
 * `subscribed=false` significa que o servico existe no armazem mas o seller
 * NAO assinou: enviar estoque contando com ele resulta em recusa.
 */
final class FbtLogisticsService implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?bool $subscribed = null,
    ) {}
}
