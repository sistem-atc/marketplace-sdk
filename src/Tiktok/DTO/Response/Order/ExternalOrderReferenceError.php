<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Falha de UMA entrada do lote de `addExternalOrderReferences`.
 *
 * `code` aqui e' STRING ("36020001") — nao confundir com o `code` int do
 * envelope da resposta, que continua 0 (sucesso) mesmo com erros nesta lista.
 */
final class ExternalOrderReferenceError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo de negocio da falha, STRING (ex.: "36020001"). */
        public readonly ?string $code = null,
        public readonly ?string $message = null,
        public readonly ?ExternalOrderReferenceErrorDetail $detail = null,
    ) {}
}
