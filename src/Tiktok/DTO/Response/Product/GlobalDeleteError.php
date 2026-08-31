<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Falha de exclusao de UM produto global. Aqui `code` e' INT (codigo de erro
 * de negocio, ex. 12019114) — diferente dos ids, que sao string.
 */
final class GlobalDeleteError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $message = null,
        public readonly ?int $code = null,
        public readonly ?GlobalDeleteErrorDetail $detail = null,
    ) {}
}
