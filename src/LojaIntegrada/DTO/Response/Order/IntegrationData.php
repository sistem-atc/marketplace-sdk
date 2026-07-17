<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;


/** integration_data (quando o pedido veio de um marketplace via LI). */
final class IntegrationData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly mixed $integrator = null,
        public readonly mixed $marketplace = null,
        public readonly ?string $reference = null,
    ) {}
}
