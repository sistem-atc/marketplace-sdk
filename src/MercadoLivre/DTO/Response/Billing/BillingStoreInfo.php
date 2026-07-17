<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `store_info` — loja/CD de origem da cobrança (ex: "Cajamar Via de Acesso Norte").
 */
final class BillingStoreInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $storeId = null,
        public readonly ?string $storeName = null,
    ) {}
}
