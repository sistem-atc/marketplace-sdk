<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Item de `package_list` do webhook type 4. */
final class PackageUpdateItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $orderIdList */
    public function __construct(
        public readonly ?string $packageId = null,
        /**
         * Pedidos que hoje moram nesse pacote. Lista de string crua (a API
         * manda ids, nao objetos) — 2+ ids significa pacote COMBINADO, e o
         * combine e' o que faz varios pedidos irem numa NF-e/etiqueta so'.
         */
        public readonly ?array $orderIdList = null,
    ) {}
}
