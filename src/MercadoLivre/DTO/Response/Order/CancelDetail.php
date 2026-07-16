<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Detalhe de cancelamento (`cancel_detail`) — motivo, quem pediu, quando.
 */
final class CancelDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $date = null,
        public readonly ?string $group = null,
        public readonly ?string $description = null,
        public readonly ?string $requestedBy = null,
        public readonly ?string $applicationId = null,
    ) {}
}
