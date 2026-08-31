<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Falha PARCIAL da replicacao (`errors[]`).
 *
 * `code` aqui e' o codigo de NEGOCIO por mercado (12052223, 12052500...), sem
 * relacao com o `code` do envelope HTTP — que vem 0 mesmo quando tudo falhou.
 */
final class GlobalReplicateError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $code = null,
        public readonly ?string $message = null,
        public readonly ?GlobalReplicateErrorDetail $detail = null,
    ) {}
}
