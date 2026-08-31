<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de validade que o FBT IMPOE a categoria (nao o que o seller declarou).
 * Todos os dias sao contados pra tras a partir do vencimento do lote.
 */
final class FbtExpirationBaseInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $inboundCutoffDays = null,
        public readonly ?int $expirationAlertDays = null,
        public readonly ?int $salesCutoffDays = null,
    ) {}
}
